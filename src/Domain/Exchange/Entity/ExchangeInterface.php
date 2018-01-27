<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 14.01.2018
 * Time: 13:39
 */

namespace Domain\Exchange\Entity;


use Domain\Exception\EntityNotFoundException;
use Domain\Exchange\ValueObject\ExchangeId;
use Domain\Exchange\ValueObject\OrderId;

interface ExchangeInterface
{
	/**
	 * @return ExchangeId
	 */
	public function getId(): ExchangeId;

	public function createOrder(Order $order);

	public function cancelOrder(Order $order);

	public function getSymbol(string $symbol);

	public function getFee();

	public function getBid(string $symbol): float;

	public function getAsk(string $symbol): float;

	/**
	 * @return Order[]
	 */
	public function getActiveOrders(): array;

	/**
	 * @param OrderId $orderId
	 * @return Order
	 * @throws EntityNotFoundException
	 */
	public function getOrder(OrderId $orderId): Order;
}