<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 14.01.2018
 * Time: 16:13
 */

namespace Domain\Exchange\Entity;


use Domain\Exchange\ValueObject\TradingStrategyId;

interface TradingStrategyInterface
{
	public function getId(): TradingStrategyId;

	public function isNeedToStartTrading(): bool;

	public function processTrading(BotTradingSession $session);
}