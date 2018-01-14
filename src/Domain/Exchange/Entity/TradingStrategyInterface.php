<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 14.01.2018
 * Time: 16:13
 */

namespace Domain\Exchange\Entity;


interface TradingStrategyInterface
{
	public function isNeedToStartTrading(): bool;
}