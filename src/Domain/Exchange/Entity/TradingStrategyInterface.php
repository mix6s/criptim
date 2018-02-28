<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 14.01.2018
 * Time: 16:13
 */

namespace Domain\Exchange\Entity;


use Domain\Exchange\ValueObject\TradingStrategyId;
use Domain\Exchange\ValueObject\TradingStrategySettings;

interface TradingStrategyInterface
{
	public function getId(): TradingStrategyId;

	public function isNeedToStartTrading(Bot $bot): bool;

	public function processTrading(BotTradingSession $session);
}