<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 15.01.2018
 * Time: 11:24
 */

namespace Domain\Exchange\Entity\TradingStrategy;


use Domain\Exchange\Entity\BotTradingSession;
use Domain\Exchange\Entity\TradingStrategyInterface;
use Domain\Exchange\ValueObject\TradingStrategyId;
use Domain\Exchange\ValueObject\TradingStrategySettings;

class Ema implements TradingStrategyInterface
{

	public function getId(): TradingStrategyId
	{
		return TradingStrategyId::EMA();
	}

	public function isNeedToStartTrading(TradingStrategySettings $settings): bool
	{
		// TODO: Implement isNeedToStartTrading() method.
	}

	public function processTrading(BotTradingSession $session)
	{
		// TODO: Implement processTrading() method.
	}
}