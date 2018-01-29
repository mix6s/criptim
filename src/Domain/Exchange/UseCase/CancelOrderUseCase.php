<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 29.01.2018
 * Time: 22:47
 */

namespace Domain\Exchange\UseCase;


use Domain\Exchange\Repository\BotTradingSessionRepositoryInterface;
use Domain\Exchange\Repository\ExchangeRepositoryInterface;
use Domain\Exchange\Repository\OrderRepositoryInterface;
use Domain\Exchange\UseCase\Request\CancelOrderRequest;
use Domain\Exchange\UseCase\Request\UpdateOrderRequest;
use Domain\Exchange\UseCase\Response\CancelOrderResponse;

class CancelOrderUseCase
{
	/**
	 * @var BotTradingSessionRepositoryInterface
	 */
	private $botTradingSessionRepository;
	/**
	 * @var ExchangeRepositoryInterface
	 */
	private $exchangeRepository;
	/**
	 * @var OrderRepositoryInterface
	 */
	private $orderRepository;
	/**
	 * @var UpdateOrderUseCase
	 */
	private $updateOrderUseCase;

	public function __construct(
		BotTradingSessionRepositoryInterface $botTradingSessionRepository,
		ExchangeRepositoryInterface $exchangeRepository,
		OrderRepositoryInterface $orderRepository,
		UpdateOrderUseCase $updateOrderUseCase
	)
	{
		$this->botTradingSessionRepository = $botTradingSessionRepository;
		$this->exchangeRepository = $exchangeRepository;
		$this->orderRepository = $orderRepository;
		$this->updateOrderUseCase = $updateOrderUseCase;
	}

	public function execute(CancelOrderRequest $request): CancelOrderResponse
	{
		$order = $this->orderRepository->findById($request->getOrderId());
		$session = $this->botTradingSessionRepository->findById($order->getBotTradingSessionId());
		$exchange = $this->exchangeRepository->findById($session->getExchangeId());
		$exchangeOrder = $exchange->cancelOrder($order->getId());
		$updateRequest = new UpdateOrderRequest();
		$updateRequest->setExchangeOrder($exchangeOrder);
		$this->updateOrderUseCase->execute($updateRequest);
		return new CancelOrderResponse();
	}
}