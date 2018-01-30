<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/27/18
 * Time: 3:43 PM
 */

namespace Domain\Exchange\UseCase;


use Domain\Exception\EntityNotFoundException;
use Domain\Exchange\Policy\MoneyFromFloatPolicy;
use Domain\Exchange\Repository\BotTradingSessionAccountTransactionRepositoryInterface;
use Domain\Exchange\Repository\BotTradingSessionRepositoryInterface;
use Domain\Exchange\Repository\ExchangeRepositoryInterface;
use Domain\Exchange\Repository\OrderRepositoryInterface;
use Domain\Exchange\UseCase\Request\GetBotTradingSessionAccountRequest;
use Domain\Exchange\UseCase\Request\GetBotTradingSessionBalancesRequest;
use Domain\Exchange\UseCase\Response\GetBotTradingSessionBalancesResponse;
use Money\Money;

class GetBotTradingSessionBalancesUseCase
{
	/**
	 * @var GetBotTradingSessionAccountUseCase
	 */
	private $getBotTradingSessionAccountUseCase;
	/**
	 * @var OrderRepositoryInterface
	 */
	private $orderRepository;
	/**
	 * @var ExchangeRepositoryInterface
	 */
	private $exchangeRepository;
	/**
	 * @var BotTradingSessionRepositoryInterface
	 */
	private $botTradingSessionRepository;
	/** @var MoneyFromFloatPolicy  */
	private $moneyFromFloatPolicy;
	/**
	 * @var BotTradingSessionAccountTransactionRepositoryInterface
	 */
	private $botTradingSessionAccountTransactionRepository;

	public function __construct(
		GetBotTradingSessionAccountUseCase $getBotTradingSessionAccountUseCase,
		OrderRepositoryInterface $orderRepository,
		ExchangeRepositoryInterface $exchangeRepository,
		BotTradingSessionRepositoryInterface $botTradingSessionRepository,
		BotTradingSessionAccountTransactionRepositoryInterface $botTradingSessionAccountTransactionRepository
	)
	{
		$this->getBotTradingSessionAccountUseCase = $getBotTradingSessionAccountUseCase;
		$this->orderRepository = $orderRepository;
		$this->moneyFromFloatPolicy = new MoneyFromFloatPolicy();
		$this->exchangeRepository = $exchangeRepository;
		$this->botTradingSessionRepository = $botTradingSessionRepository;
		$this->botTradingSessionAccountTransactionRepository = $botTradingSessionAccountTransactionRepository;
	}

	public function execute(GetBotTradingSessionBalancesRequest $request): GetBotTradingSessionBalancesResponse
	{
		$session = $this->botTradingSessionRepository->findById($request->getBotTradingSessionId());
		$exchange = $this->exchangeRepository->findById($session->getExchangeId());

		$accRequest = new GetBotTradingSessionAccountRequest();
		$accRequest->setBotTradingSessionId($request->getBotTradingSessionId());
		$accRequest->setCurrency($request->getCurrency());
		$account = $this->getBotTradingSessionAccountUseCase->execute($accRequest)->getBotTradingSessionAccount();

		$activeOrders = $this->orderRepository->findActive($request->getBotTradingSessionId());
		$inOrderBalance = new Money(0, $request->getCurrency());
		foreach ($activeOrders as $order) {
			if (!$order->isActive()) {
				continue;
			}
			if ($request->getCurrency()->equals($order->getSymbol()->getCounterCurrency())) {
				if ($order->getType() == 'sell') {
					continue;
				}
				$orderTotal = $this->moneyFromFloatPolicy->getMoney($request->getCurrency(), $order->getPrice())
					->multiply($order->getAmount())
					->multiply(1 + $exchange->getFee());
			} elseif ($request->getCurrency()->equals($order->getSymbol()->getBaseCurrency())) {
				if ($order->getType() == 'buy') {
					continue;
				}
				$orderTotal = $this->moneyFromFloatPolicy->getMoney($request->getCurrency(), $order->getAmount());
			} else {
				continue;
			}
			$inOrderBalance = $inOrderBalance->add($orderTotal);
		}
		$availableBalance = $account->getBalance()->subtract($inOrderBalance);

		try {
			$startBalance = $this->botTradingSessionAccountTransactionRepository
				->findLastBySessionIdCurrencyDate($session->getId(), $request->getCurrency(), $session->getCreatedAt())->getBalance();
		} catch (EntityNotFoundException $exception) {
			$startBalance = new Money(0, $request->getCurrency());
		}

		return new GetBotTradingSessionBalancesResponse($account, $account->getBalance(), $inOrderBalance, $availableBalance, $startBalance);
	}
}