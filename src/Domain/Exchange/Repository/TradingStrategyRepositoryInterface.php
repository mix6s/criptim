<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 14.01.2018
 * Time: 16:04
 */

namespace Domain\Exchange\Repository;


use Domain\Exchange\Entity\TradingStrategyInterface;
use Domain\Exchange\ValueObject\TradingStrategyId;

interface TradingStrategyRepositoryInterface
{
	public function findById(TradingStrategyId $tradingStrategyId): TradingStrategyInterface;
}