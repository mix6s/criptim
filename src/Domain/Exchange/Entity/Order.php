<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 15.01.2018
 * Time: 14:01
 */

namespace Domain\Exchange\Entity;


use Domain\Exchange\ValueObject\BotTradingSessionId;
use Domain\Exchange\ValueObject\OrderId;
use Money\CurrencyPair;

class Order
{
	const STATUS_NEW = 'new';
	const STATUS_PARTIALLY_FILLED = 'partiallyFilled';
	const STATUS_FILLED = 'filled';
	const STATUS_CANCELED = 'canceled';

	/**
	 * @var OrderId
	 */
	private $id;
	/**
	 * @var BotTradingSessionId
	 */
	private $botTradingSessionId;
	/**
	 * @var string
	 */
	private $type;
	/**
	 * @var float
	 */
	private $price;
	/**
	 * @var float
	 */
	private $amount;
	/**
	 * @var CurrencyPair
	 */
	private $symbol;
	/**
	 * @var string
	 */
	private $status;
	/**
	 * @var float
	 */
	private $execAmount;
	/**
	 * @var \DateTimeImmutable
	 */
	private $createdAt;
	/**
	 * @var \DateTimeImmutable
	 */
	private $updatedAt;

	public function __construct(OrderId $id, BotTradingSessionId $botTradingSessionId, string $type, float $price, float $amount, CurrencyPair $symbol)
	{
		$this->id = $id;
		$this->botTradingSessionId = $botTradingSessionId;
		$this->type = $type;
		$this->price = $price;
		$this->amount = $amount;
		$this->symbol = $symbol;
		$this->status = self::STATUS_NEW;
		$this->execAmount = 0.;
		$this->createdAt = new \DateTimeImmutable();
		$this->updatedAt = new \DateTimeImmutable();
	}

	/**
	 * @return string
	 */
	public function getStatus(): string
	{
		return $this->status;
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return $this->type;
	}

	/**
	 * @return float
	 */
	public function getPrice(): float
	{
		return $this->price;
	}

	/**
	 * @return float
	 */
	public function getExecAmount(): float
	{
		return $this->execAmount;
	}

	/**
	 * @return CurrencyPair
	 */
	public function getSymbol(): CurrencyPair
	{
		return $this->symbol;
	}

	/**
	 * @return float
	 */
	public function getAmount(): float
	{
		return $this->amount;
	}

	public function isActive(): bool
	{
		return in_array($this->status, [self::STATUS_NEW, self::STATUS_PARTIALLY_FILLED]);
	}

	/**
	 * @return OrderId
	 */
	public function getId(): OrderId
	{
		return $this->id;
	}

	public function updateFrom(ExchangeOrder $order)
	{
		if ($order->getStatus() !== null) {
			$this->status = $order->getStatus();
		}

		if ($order->getPrice() !== null) {
			$this->price = $order->getPrice();
		}
		if ($order->getAmount() !== null) {
			$this->amount = $order->getAmount();
		}
		if ($order->getExecAmount() !== null) {
			$this->execAmount = $order->getExecAmount();
		}
		$this->updatedAt = new \DateTimeImmutable();
	}

	/**
	 * @return BotTradingSessionId
	 */
	public function getBotTradingSessionId(): BotTradingSessionId
	{
		return $this->botTradingSessionId;
	}

	/**
	 * @return \DateTimeImmutable
	 */
	public function getCreatedAt(): \DateTimeImmutable
	{
		return $this->createdAt;
	}

	/**
	 * @return \DateTimeImmutable
	 */
	public function getUpdatedAt(): \DateTimeImmutable
	{
		return $this->updatedAt;
	}
}