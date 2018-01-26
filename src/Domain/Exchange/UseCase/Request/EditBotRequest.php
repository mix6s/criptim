<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/25/18
 * Time: 11:08 AM
 */

namespace Domain\Exchange\UseCase\Request;


use Domain\Exchange\ValueObject\BotId;
use Domain\Exchange\ValueObject\ExchangeId;
use Domain\Exchange\ValueObject\TradingStrategyId;
use Domain\Exchange\ValueObject\TradingStrategySettings;

class EditBotRequest
{
	/** @var BotId|null */
	private $botId;
	/** @var ExchangeId|null */
	private $exchangeId;
	/** @var TradingStrategyId|null */
	private $tradingStrategyId;
	/** @var TradingStrategySettings|null */
	private $tradingStrategySettings;
	/** @var  string|null */
	private $status;
	/**
	 * @return ExchangeId|null
	 */
	public function getExchangeId()
	{
		return $this->exchangeId;
	}

	/**
	 * @param ExchangeId $exchangeId
	 */
	public function setExchangeId(ExchangeId $exchangeId)
	{
		$this->exchangeId = $exchangeId;
	}

	/**
	 * @return TradingStrategyId|null
	 */
	public function getTradingStrategyId()
	{
		return $this->tradingStrategyId;
	}

	/**
	 * @param TradingStrategyId $tradingStrategyId
	 */
	public function setTradingStrategyId(TradingStrategyId $tradingStrategyId)
	{
		$this->tradingStrategyId = $tradingStrategyId;
	}

	/**
	 * @return TradingStrategySettings| null
	 */
	public function getTradingStrategySettings()
	{
		return $this->tradingStrategySettings;
	}

	/**
	 * @param TradingStrategySettings $tradingStrategySettings
	 */
	public function setTradingStrategySettings(TradingStrategySettings $tradingStrategySettings)
	{
		$this->tradingStrategySettings = $tradingStrategySettings;
	}

	/**
	 * @return BotId
	 */
	public function getBotId()
	{
		return $this->botId;
	}

	/**
	 * @param BotId $botId
	 */
	public function setBotId(BotId $botId)
	{
		$this->botId = $botId;
	}

	/**
	 * @return null|string
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * @param null|string $status
	 */
	public function setStatus($status)
	{
		$this->status = $status;
	}
}