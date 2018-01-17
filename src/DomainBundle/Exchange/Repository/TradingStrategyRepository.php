<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/17/18
 * Time: 5:43 PM
 */

namespace DomainBundle\Exchange\Repository;


use Domain\Exchange\Entity\TradingStrategyInterface;
use Domain\Exchange\Repository\TradingStrategyRepositoryInterface;
use Domain\Exchange\ValueObject\TradingStrategyId;

class TradingStrategyRepository implements TradingStrategyRepositoryInterface
{

	public function findById(TradingStrategyId $tradingStrategyId): TradingStrategyInterface
	{
		// TODO: Implement findById() method.
	}
}