<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 14.01.2018
 * Time: 15:23
 */

namespace Domain\Exchange\UseCase\Request;


use Domain\Exchange\ValueObject\BotId;
use Domain\Exchange\ValueObject\TradingStrategyId;
use Domain\Exchange\ValueObject\TradingStrategySettings;

class ProcessBotTradingRequest
{
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

	public function __construct(
		BotId $botId,
		TradingStrategyId $tradingStrategyId,
		TradingStrategySettings $tradingStrategySettings
	) {
		$this->botId = $botId;
		$this->tradingStrategyId = $tradingStrategyId;
		$this->tradingStrategySettings = $tradingStrategySettings;
	}

	/**
	 * @return BotId
	 */
	public function getBotId(): BotId
	{
		return $this->botId;
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
}