<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 14.01.2018
 * Time: 13:39
 */

namespace Domain\Exchange\Entity;


use Domain\Exchange\ValueObject\ExchangeId;
use Domain\Exchange\ValueObject\OrderId;

interface ExchangeInterface
{
	/**
	 * @return ExchangeId
	 */
	public function getId(): ExchangeId;

	public function createOrder(OrderId $orderId);

	public function cancelOrder(OrderId $orderId);

	public function getSymbol(string $symbol);
}