<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 29.01.2018
 * Time: 21:15
 */

namespace Domain\Exchange\UseCase;


use Domain\Exchange\Entity\BotTradingSessionAccountTransaction;
use Domain\Exchange\Entity\Order;
use Domain\Exchange\Factory\IdFactoryInterface;
use Domain\Exchange\Policy\MoneyFromFloatPolicy;
use Domain\Exchange\Repository\BotTradingSessionAccountRepositoryInterface;
use Domain\Exchange\Repository\BotTradingSessionAccountTransactionRepositoryInterface;
use Domain\Exchange\Repository\BotTradingSessionRepositoryInterface;
use Domain\Exchange\Repository\ExchangeRepositoryInterface;
use Domain\Exchange\Repository\OrderRepositoryInterface;
use Domain\Exchange\UseCase\Request\GetBotTradingSessionAccountRequest;
use Domain\Exchange\UseCase\Request\UpdateOrderRequest;
use Domain\Exchange\UseCase\Response\UpdateOrderResponse;

class UpdateOrderUseCase
{
	/**
	 * @var OrderRepositoryInterface
	 */
	private $orderRepository;
	/**
	 * @var GetBotTradingSessionAccountUseCase
	 */
	private $getBotTradingSessionAccountUseCase;
	/**
	 * @var BotTradingSessionAccountTransactionRepositoryInterface
	 */
	private $botTradingSessionAccountTransactionRepository;
	/**
	 * @var MoneyFromFloatPolicy
	 */
	private $moneyFromFloatPolicy;
	/**
	 * @var BotTradingSessionRepositoryInterface
	 */
	private $botTradingSessionRepository;
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

	public function __construct(
		OrderRepositoryInterface $orderRepository,
		BotTradingSessionRepositoryInterface $botTradingSessionRepository,
		ExchangeRepositoryInterface $exchangeRepository,
		GetBotTradingSessionAccountUseCase $getBotTradingSessionAccountUseCase,
		BotTradingSessionAccountRepositoryInterface $botTradingSessionAccountRepository,
		BotTradingSessionAccountTransactionRepositoryInterface $botTradingSessionAccountTransactionRepository,
		IdFactoryInterface $idFactory
	)
	{
		$this->orderRepository = $orderRepository;
		$this->getBotTradingSessionAccountUseCase = $getBotTradingSessionAccountUseCase;
		$this->botTradingSessionAccountTransactionRepository = $botTradingSessionAccountTransactionRepository;
		$this->moneyFromFloatPolicy = new MoneyFromFloatPolicy();
		$this->botTradingSessionRepository = $botTradingSessionRepository;
		$this->exchangeRepository = $exchangeRepository;
		$this->botTradingSessionAccountRepository = $botTradingSessionAccountRepository;
		$this->idFactory = $idFactory;
	}

	public function execute(UpdateOrderRequest $request): UpdateOrderResponse
	{
		$exchangeOrder = $request->getExchangeOrder();
		$order = $this->orderRepository->findById($exchangeOrder->getId());
		if ($exchangeOrder->getStatus() === null) {
			$order->updateFrom($exchangeOrder);
			$this->orderRepository->save($order);
			return new UpdateOrderResponse();
		}
		if ($order->getStatus() === $exchangeOrder->getStatus()) {
			$order->updateFrom($exchangeOrder);
			$this->orderRepository->save($order);
			return new UpdateOrderResponse();
		}
		if (!in_array($exchangeOrder->getStatus(), [Order::STATUS_FILLED, Order::STATUS_CANCELED])) {
			$order->updateFrom($exchangeOrder);
			$this->orderRepository->save($order);
			return new UpdateOrderResponse();
		}

		$session = $this->botTradingSessionRepository->findById($order->getBotTradingSessionId());
		$exchange = $this->exchangeRepository->findById($session->getExchangeId());

		$order->updateFrom($exchangeOrder);

		$accRequest = new GetBotTradingSessionAccountRequest();
		$accRequest->setBotTradingSessionId($order->getBotTradingSessionId());

		$accRequest->setCurrency($order->getSymbol()->getBaseCurrency());
		$baseCurrencyAccount = $this->getBotTradingSessionAccountUseCase->execute($accRequest)
			->getBotTradingSessionAccount();

		$accRequest->setCurrency($order->getSymbol()->getCounterCurrency());
		$quoteCurrencyAccount = $this->getBotTradingSessionAccountUseCase->execute($accRequest)
			->getBotTradingSessionAccount();

		$quoteTotal = $this->moneyFromFloatPolicy
			->getMoney($quoteCurrencyAccount->getCurrency(), $order->getPrice())
			->multiply($order->getExecAmount())
			->multiply(1 + $exchange->getFee())
			->multiply($order->getType() === 'buy' ? -1 : 1);

		$baseTotal = $this->moneyFromFloatPolicy
			->getMoney($baseCurrencyAccount->getCurrency(), $order->getExecAmount())
			->multiply($order->getType() === 'buy' ? 1 : -1);

		if (!$baseTotal->isZero()) {
			$baseCurrencyAccount->change($baseTotal);
			$baseSessionAccountTransaction = new BotTradingSessionAccountTransaction(
				$this->idFactory->getBotTradingSessionAccountTransactionId(),
				$session->getId(),
				$baseCurrencyAccount->getCurrency(),
				$baseTotal,
				$baseCurrencyAccount->getBalance(),
				BotTradingSessionAccountTransaction::TYPE_ORDER_EXEC
			);
			$this->botTradingSessionAccountTransactionRepository->save($baseSessionAccountTransaction);
			$this->botTradingSessionAccountRepository->save($baseCurrencyAccount);
		}

		if ($quoteTotal->isZero()) {
			$quoteCurrencyAccount->change($quoteTotal);
			$quoteSessionAccountTransaction = new BotTradingSessionAccountTransaction(
				$this->idFactory->getBotTradingSessionAccountTransactionId(),
				$session->getId(),
				$quoteCurrencyAccount->getCurrency(),
				$quoteTotal,
				$quoteCurrencyAccount->getBalance(),
				BotTradingSessionAccountTransaction::TYPE_ORDER_EXEC
			);
			$this->botTradingSessionAccountTransactionRepository->save($quoteSessionAccountTransaction);
			$this->botTradingSessionAccountRepository->save($quoteCurrencyAccount);
		}

		$this->orderRepository->save($order);
		return new UpdateOrderResponse();
	}
}