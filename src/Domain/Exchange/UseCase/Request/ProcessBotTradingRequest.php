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

	public function __construct(
		BotId $botId
	) {
		$this->botId = $botId;
	}

	/**
	 * @return BotId
	 */
	public function getBotId(): BotId
	{
		return $this->botId;
	}
}