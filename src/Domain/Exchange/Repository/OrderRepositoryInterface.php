<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 15.01.2018
 * Time: 15:33
 */

namespace Domain\Exchange\Repository;


use Domain\Exception\EntityNotFoundException;
use Domain\Exchange\Entity\Order;
use Domain\Exchange\ValueObject\BotTradingSessionId;
use Domain\Exchange\ValueObject\ExchangeId;
use Domain\Exchange\ValueObject\OrderId;

interface OrderRepositoryInterface
{
	public function save(Order $order);

	/**
	 * @param BotTradingSessionId $sessionId
	 * @return Order[]
	 */
	public function findActive(BotTradingSessionId $sessionId): array;

	/**
	 * @param OrderId $orderId
	 * @return Order
	 * @throws EntityNotFoundException
	 */
	public function findById(OrderId $orderId): Order;

	/**
	 * @param BotTradingSessionId $sessionId
	 * @return Order
	 * @throws EntityNotFoundException
	 */
	public function findLastSell(BotTradingSessionId $sessionId): Order;

	/**
	 * @param BotTradingSessionId $sessionId
	 * @return Order
	 * @throws EntityNotFoundException
	 */
	public function findFirstBuy(BotTradingSessionId $sessionId): Order;
	public function findLastBuy(BotTradingSessionId $sessionId): Order;
	public function countFilledBuyOrders(BotTradingSessionId $sessionId): int;

	/**
	 * @param ExchangeId $exchangeId
	 * @return Order[]
	 */
	public function findActiveByExchangeId(ExchangeId $exchangeId): array;
}