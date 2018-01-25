<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/17/18
 * Time: 5:43 PM
 */

namespace DomainBundle\Exchange\Repository;


use Domain\Exception\EntityNotFoundException;
use Domain\Exchange\Entity\TradingStrategyInterface;
use Domain\Exchange\Repository\TradingStrategyRepositoryInterface;
use Domain\Exchange\ValueObject\TradingStrategyId;
use DomainException;

class TradingStrategyRepository implements TradingStrategyRepositoryInterface
{
	/**
	 * @var TradingStrategyInterface[]
	 */
	private $strategies;

	public function __construct(array $strategies = [])
	{
		foreach ($strategies as $strategy) {
			if (!$strategy instanceof TradingStrategyInterface) {
				throw new DomainException('Invalid TradingStrategy class');
			}
			if (isset($this->strategies[(string)$strategy->getId()])) {
				throw new DomainException(sprintf('TradingStrategy with id %s already exist in repository',
					$strategy->getId()));
			}
			$this->strategies[(string)$strategy->getId()] = $strategy;
		}
	}

	public function findById(TradingStrategyId $tradingStrategyId): TradingStrategyInterface
	{
		$strategy = $this->strategies[(string)$tradingStrategyId] ?? null;
		if ($strategy === null) {
			throw new EntityNotFoundException(sprintf('TradingStrategy with id %s not found', $tradingStrategyId));
		}
		return $strategy;
	}

	/**
	 * @return TradingStrategyInterface[]
	 */
	public function findAll(): array
	{
		return $this->strategies;
	}
}