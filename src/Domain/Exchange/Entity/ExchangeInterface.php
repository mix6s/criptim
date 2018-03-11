<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 14.01.2018
 * Time: 13:39
 */

namespace Domain\Exchange\Entity;


use Domain\Exception\EntityNotFoundException;
use Domain\Exchange\ValueObject\Candle;
use Domain\Exchange\ValueObject\ExchangeId;
use Domain\Exchange\ValueObject\OrderId;
use Money\Currency;

interface ExchangeInterface
{
	public function getId(): ExchangeId;

	public function createOrder(Order $order);

	public function cancelOrder(OrderId $orderId): ExchangeOrder;

	public function getSymbolForCurrencies(Currency $base, Currency $quote): string;

	public function getFee(): float;

	public function getBid(string $symbol): float;

	public function getAsk(string $symbol): float;

	public function getPriceTickSize(string $symbol): float;
	public function getAmountIncrement(string $symbol): float;

	/**
	 * @param Currency $base
	 * @param Currency $quote
	 * @param \DateInterval $period
	 * @param int $count
	 * @return Candle[]
	 */
	public function getCandles(Currency $base, Currency $quote, \DateInterval $period, int $count): array;

	/**
	 * @return ExchangeOrder[]
	 */
	public function getActiveOrders(): array;

	/**
	 * @param OrderId $orderId
	 * @return ExchangeOrder
	 * @throws EntityNotFoundException
	 */
	public function getOrder(OrderId $orderId): ExchangeOrder;
}