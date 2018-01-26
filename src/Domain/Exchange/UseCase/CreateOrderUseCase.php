<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/26/18
 * Time: 8:22 PM
 */

namespace Domain\Exchange\UseCase;


use Domain\Exception\DomainException;
use Domain\Exchange\Entity\Order;
use Domain\Exchange\Factory\IdFactoryInterface;
use Domain\Exchange\Policy\MoneyFromFloatPolicy;
use Domain\Exchange\Repository\BotTradingSessionAccountRepositoryInterface;
use Domain\Exchange\Repository\ExchangeRepositoryInterface;
use Domain\Exchange\Repository\OrderRepositoryInterface;
use Domain\Exchange\UseCase\Request\CreateOrderRequest;
use Domain\Exchange\UseCase\Request\GetBotTradingSessionAccountRequest;
use Domain\Exchange\UseCase\Response\CreateOrderResponse;
use Money\Currency;
use Money\Money;

class CreateOrderUseCase
{
	/**
	 * @var ExchangeRepositoryInterface
	 */
	private $exchangeRepository;
	/**
	 * @var BotTradingSessionAccountRepositoryInterface
	 */
	private $botTradingSessionAccountRepository;
	/**
	 * @var GetBotTradingSessionAccountUseCase
	 */
	private $getBotTradingSessionAccountUseCase;
	/**
	 * @var IdFactoryInterface
	 */
	private $idFactory;
	/**
	 * @var OrderRepositoryInterface
	 */
	private $orderRepository;
	/**
	 * @var MoneyFromFloatPolicy
	 */
	private $moneyFromFloatPolicy;

	public function __construct(
		ExchangeRepositoryInterface $exchangeRepository,
		BotTradingSessionAccountRepositoryInterface $botTradingSessionAccountRepository,
		GetBotTradingSessionAccountUseCase $getBotTradingSessionAccountUseCase,
		IdFactoryInterface $idFactory,
		OrderRepositoryInterface $orderRepository
	)
	{
		$this->exchangeRepository = $exchangeRepository;
		$this->botTradingSessionAccountRepository = $botTradingSessionAccountRepository;
		$this->getBotTradingSessionAccountUseCase = $getBotTradingSessionAccountUseCase;
		$this->idFactory = $idFactory;
		$this->orderRepository = $orderRepository;
		$this->moneyFromFloatPolicy = new MoneyFromFloatPolicy();
	}

	public function execute(CreateOrderRequest $request): CreateOrderResponse
	{
		$sessionId = $request->getBotTradingSessionId();
		$exchange = $this->exchangeRepository->findById($request->getExchangeId());

		if ($request->getType() === 'buy') {
			$currency = $request->getSymbol()->getCounterCurrency();
		} else {
			$currency = $request->getSymbol()->getBaseCurrency();
		}
		$accRequest = new GetBotTradingSessionAccountRequest();
		$accRequest->setBotTradingSessionId($sessionId);
		$accRequest->setCurrency($currency);

		$account = $this->getBotTradingSessionAccountUseCase->execute($accRequest)->getBotTradingSessionAccount();

		$activeOrders = $this->orderRepository->findActive($sessionId);
		$inOrderBalance = new Money(0, $currency);
		foreach ($activeOrders as $order) {
			if ($order->getType() !== $request->getType()) {
				continue;
			}
			$orderAmount = $this->moneyFromFloatPolicy->getMoney($currency, $request->getAmount());
			if ($order->getType() === 'buy') {
				$orderTotal = $orderAmount->multiply($request->getPrice())->multiply(1 + $exchange->getFee());
			} else {
				$orderTotal = $orderAmount->multiply($request->getPrice());
			}
			$inOrderBalance = $inOrderBalance->add($orderTotal);
		}

		$availableBalance = $account->getBalance()->subtract($inOrderBalance);
		$amount = $this->moneyFromFloatPolicy->getMoney($currency, $request->getAmount());
		if ($request->getType() === 'buy') {
			$total = $amount->multiply($request->getPrice())->multiply(1 + $exchange->getFee());
		} else {
			$total = $amount->multiply($request->getPrice());
		}

		if ($availableBalance->lessThanOrEqual($total)) {
			throw new DomainException("Insufficient funds");
		}

		$orderId = $this->idFactory->getOrderId();
		$symbol = $request->getSymbol()->getBaseCurrency()->getCode() . $request->getSymbol()->getCounterCurrency()->getCode();
		$order = new Order($orderId, $sessionId, $request->getType(), $request->getPrice(), $request->getAmount(), $symbol);
		$this->orderRepository->save($order);
		return new CreateOrderResponse();
	}
}