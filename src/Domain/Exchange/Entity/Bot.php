<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 13.01.2018
 * Time: 22:35
 */

namespace Domain\Exchange\Entity;


use Domain\Exchange\ValueObject\BotId;
use Domain\Exchange\ValueObject\ExchangeId;
use Domain\Exchange\ValueObject\TradingStrategyId;
use Domain\Exchange\ValueObject\TradingStrategySettings;

class Bot
{
	const STATUS_ACTIVE = 'active';
	const STATUS_INACTIVE = 'inactive';
	/**
	 * @var BotId
	 */
	private $id;
	/**
	 * @var ExchangeId
	 */
	private $exchangeId;
	/**
	 * @var TradingStrategyId
	 */
	private $tradingStrategyId;
	/**
	 * @var TradingStrategySettings
	 */
	private $tradingStrategySettings;
	/**
	 * @var string
	 */
	private $status;

	public function __construct(BotId $id, ExchangeId $exchangeId, TradingStrategyId $tradingStrategyId, TradingStrategySettings $tradingStrategySettings)
	{
		$this->id = $id;
		$this->exchangeId = $exchangeId;
		$this->tradingStrategyId = $tradingStrategyId;
		$this->tradingStrategySettings = $tradingStrategySettings;
		$this->status = self::STATUS_INACTIVE;
	}

	public function changeExchangeId(ExchangeId $exchangeId)
	{
		$this->exchangeId = $exchangeId;
	}

	public function changeTradingStrategyId(TradingStrategyId $tradingStrategyId)
	{
		$this->tradingStrategyId = $tradingStrategyId;
	}

	public function changeTradingStrategySettings(TradingStrategySettings $tradingStrategySettings)
	{
		$this->tradingStrategySettings = $tradingStrategySettings;
	}


	public function changeStatus(string $status)
	{
		$this->status = $status;
	}

	public function activate()
	{
		$this->status = self::STATUS_ACTIVE;
	}

	public function deactivate()
	{
		$this->status = self::STATUS_INACTIVE;
	}

	/**
	 * @return BotId
	 */
	public function getId(): BotId
	{
		return $this->id;
	}

	/**
	 * @return ExchangeId
	 */
	public function getExchangeId(): ExchangeId
	{
		return $this->exchangeId;
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
	 * @return string
	 */
	public function getStatus(): string
	{
		return $this->status;
	}
}