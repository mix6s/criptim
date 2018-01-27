<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 15.01.2018
 * Time: 15:33
 */

namespace Domain\Exchange\Repository;


use Domain\Exchange\Entity\Order;
use Domain\Exchange\ValueObject\BotTradingSessionId;
use Domain\Exchange\ValueObject\ExchangeId;

interface OrderRepositoryInterface
{
	public function save(Order $order);

	/**
	 * @param BotTradingSessionId $sessionId
	 * @return Order[]
	 */
	public function findActive(BotTradingSessionId $sessionId): array;

	/**
	 * @param ExchangeId $exchangeId
	 * @return Order[]
	 */
	public function findActiveByExchangeId(ExchangeId $exchangeId): array;
}