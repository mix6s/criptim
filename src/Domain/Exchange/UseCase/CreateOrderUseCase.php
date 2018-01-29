<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/26/18
 * Time: 8:22 PM
 */

namespace Domain\Exchange\UseCase;


use Domain\Exception\DomainException;
use Domain\Exchange\Entity\BotTradingSessionAccountTransaction;
use Domain\Exchange\Entity\Order;
use Domain\Exchange\Factory\IdFactoryInterface;
use Domain\Exchange\Policy\MoneyFromFloatPolicy;
use Domain\Exchange\Repository\BotTradingSessionAccountRepositoryInterface;
use Domain\Exchange\Repository\BotTradingSessionAccountTransactionRepositoryInterface;
use Domain\Exchange\Repository\ExchangeRepositoryInterface;
use Domain\Exchange\Repository\OrderRepositoryInterface;
use Domain\Exchange\UseCase\Request\CreateOrderRequest;
use Domain\Exchange\UseCase\Request\GetBotTradingSessionBalancesRequest;
use Domain\Exchange\UseCase\Response\CreateOrderResponse;
use DomainBundle\Exchange\Policy\CryptoMoneyFormatter;
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
	/**
	 * @var GetBotTradingSessionBalancesUseCase
	 */
	private $getBotTradingSessionBalancesUseCase;
	/**
	 * @var BotTradingSessionAccountTransactionRepositoryInterface
	 */
	private $botTradingSessionAccountTransactionRepository;
	private $formatter;

	public function __construct(
		ExchangeRepositoryInterface $exchangeRepository,
		BotTradingSessionAccountRepositoryInterface $botTradingSessionAccountRepository,
		BotTradingSessionAccountTransactionRepositoryInterface $botTradingSessionAccountTransactionRepository,
		GetBotTradingSessionBalancesUseCase $getBotTradingSessionBalancesUseCase,
		IdFactoryInterface $idFactory,
		OrderRepositoryInterface $orderRepository
	)
	{
		$this->exchangeRepository = $exchangeRepository;
		$this->botTradingSessionAccountRepository = $botTradingSessionAccountRepository;
		$this->idFactory = $idFactory;
		$this->orderRepository = $orderRepository;
		$this->moneyFromFloatPolicy = new MoneyFromFloatPolicy();
		$this->formatter = new CryptoMoneyFormatter();
		$this->getBotTradingSessionBalancesUseCase = $getBotTradingSessionBalancesUseCase;
		$this->botTradingSessionAccountTransactionRepository = $botTradingSessionAccountTransactionRepository;
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

		$minAmount = $this->formatter->format(new Money(1, $request->getSymbol()->getBaseCurrency()));
		if ((float)$request->getAmount() < (float)$minAmount) {
			throw new DomainException(sprintf('Amount %s is too small', $request->getAmount()));
		}

		$accRequest = new GetBotTradingSessionBalancesRequest();
		$accRequest->setBotTradingSessionId($sessionId);
		$accRequest->setCurrency($currency);
		$balances = $this->getBotTradingSessionBalancesUseCase->execute($accRequest);

		if ($request->getType() === 'buy') {
			$total = $this->moneyFromFloatPolicy->getMoney($currency, $request->getPrice())
				->multiply($request->getAmount())
				->multiply(1 + $exchange->getFee());
		} else {
			$total = $this->moneyFromFloatPolicy->getMoney($currency, $request->getAmount());
		}

		if ($balances->getAvailableBalance()->lessThanOrEqual($total)) {
			throw new DomainException("Insufficient funds");
		}

		$orderId = $this->idFactory->getOrderId();;
		$order = new Order($orderId, $sessionId, $request->getType(), $request->getPrice(), $request->getAmount(), $request->getSymbol());
		$exchange->createOrder($order);
		$this->orderRepository->save($order);
		return new CreateOrderResponse($order);
	}
}