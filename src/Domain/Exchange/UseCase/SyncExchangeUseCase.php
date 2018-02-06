<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/27/18
 * Time: 5:09 PM
 */

namespace Domain\Exchange\UseCase;


use Domain\Exception\EntityNotFoundException;
use Domain\Exchange\Repository\OrderRepositoryInterface;
use Domain\Exchange\UseCase\Request\SyncExchangeRequest;
use Domain\Exchange\UseCase\Request\UpdateOrderRequest;
use DomainBundle\Exchange\Repository\ExchangeRepository;

class SyncExchangeUseCase
{
	/**
	 * @var OrderRepositoryInterface
	 */
	private $orderRepository;
	/**
	 * @var ExchangeRepository
	 */
	private $exchangeRepository;
	/**
	 * @var UpdateOrderUseCase
	 */
	private $updateOrderUseCase;

	public function __construct(
		OrderRepositoryInterface $orderRepository,
		ExchangeRepository $exchangeRepository,
		UpdateOrderUseCase $updateOrderUseCase
	)
	{
		$this->orderRepository = $orderRepository;
		$this->exchangeRepository = $exchangeRepository;
		$this->updateOrderUseCase = $updateOrderUseCase;
	}

	public function execute(SyncExchangeRequest $request)
	{
		$exchange = $this->exchangeRepository->findById($request->getExchangeId());

		$repositoryOrders = $this->orderRepository->findActiveByExchangeId($exchange->getId());
		$exchangeActiveOrders = $exchange->getActiveOrders();
		$updateOrderRequest = new UpdateOrderRequest();
		foreach ($repositoryOrders as $order) {
			$exist = false;
			foreach ($exchangeActiveOrders as $exchangeOrder) {
				if ($order->getId()->equals($exchangeOrder->getId())) {
					$exist = true;
					$updateOrderRequest->setExchangeOrder($exchangeOrder);
					$this->updateOrderUseCase->execute($updateOrderRequest);
					break;
				}
			}
			if (!$exist) {
				try {
					$exchangeOrder = $exchange->getOrder($order->getId());
				} catch (EntityNotFoundException $exception) {
					$exchange->createOrder($order);
					continue;
				}
				$updateOrderRequest->setExchangeOrder($exchangeOrder);
				$this->updateOrderUseCase->execute($updateOrderRequest);
			}
		}
	}
}