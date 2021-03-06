<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 13.01.2018
 * Time: 21:37
 */

namespace Domain\Exchange\Entity;


use Domain\Exchange\ValueObject\BotId;
use Domain\Exchange\ValueObject\BotTradingSessionId;
use Domain\Exchange\ValueObject\ExchangeId;
use Domain\Exchange\ValueObject\TradingStrategyId;
use Domain\Exchange\ValueObject\TradingStrategySettings;

class BotTradingSession
{
	const STATUS_ACTIVE = 'active';
	const STATUS_ENDED = 'ended';
	const STATUS_CLOSED = 'closed';

	/**
	 * @var BotTradingSessionId
	 */
	private $id;
	/**
	 * @var BotId
	 */
	private $botId;
	/**
	 * @var TradingStrategyId
	 */
	private $tradingStrategyId;
	/**
	 * @var TradingStrategySettings
	 */
	private $tradingStrategySettings;
	/**
	 * @var \DateTimeImmutable
	 */
	private $updatedAt;
	/**
	 * @var \DateTimeImmutable|null
	 */
	private $endedAt;
	/**
	 * @var \DateTimeImmutable
	 */
	private $createdAt;
	/**
	 * @var string
	 */
	private $status;
	/**
	 * @var ExchangeId
	 */
	private $exchangeId;

	public function __construct(BotTradingSessionId $id, BotId $botId, ExchangeId $exchangeId, TradingStrategyId $tradingStrategyId, TradingStrategySettings $tradingStrategySettings)
	{
		$this->id = $id;
		$this->botId = $botId;
		$this->tradingStrategyId = $tradingStrategyId;
		$this->tradingStrategySettings = $tradingStrategySettings;
		$this->status = self::STATUS_ACTIVE;
		$this->createdAt = new \DateTimeImmutable();
		$this->updatedAt = new \DateTimeImmutable();
		$this->endedAt = null;
		$this->exchangeId = $exchangeId;
	}


	public function end()
	{
		$this->endedAt = new \DateTimeImmutable();
		$this->status = self::STATUS_ENDED;
	}

	public function close()
	{
		$this->status = self::STATUS_CLOSED;
	}

	public function process()
	{
		$this->updatedAt = new \DateTimeImmutable();
		$this->status = self::STATUS_ACTIVE;
	}

	/**
	 * @return string
	 */
	public function getStatus(): string
	{
		return $this->status;
	}

	/**
	 * @return TradingStrategyId
	 */
	public function getTradingStrategyId(): TradingStrategyId
	{
		return $this->tradingStrategyId;
	}

	/**
	 * @return TradingStrategySettings
	 */
	public function getTradingStrategySettings(): TradingStrategySettings
	{
		return $this->tradingStrategySettings;
	}

	/**
	 * @return BotTradingSessionId
	 */
	public function getId(): BotTradingSessionId
	{
		return $this->id;
	}

	/**
	 * @return \DateTimeImmutable
	 */
	public function getCreatedAt(): \DateTimeImmutable
	{
		return $this->createdAt;
	}

	/**
	 * @return BotId
	 */
	public function getBotId(): BotId
	{
		return $this->botId;
	}

	/**
	 * @return \DateTimeImmutable
	 */
	public function getUpdatedAt(): \DateTimeImmutable
	{
		return $this->updatedAt;
	}

	/**
	 * @return \DateTimeImmutable|null
	 */
	public function getEndedAt()
	{
		return $this->endedAt;
	}

	/**
	 * @return ExchangeId
	 */
	public function getExchangeId(): ExchangeId
	{
		return $this->exchangeId;
	}
}