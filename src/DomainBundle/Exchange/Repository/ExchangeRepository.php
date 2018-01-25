<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/17/18
 * Time: 5:42 PM
 */

namespace DomainBundle\Exchange\Repository;


use Domain\Exception\DomainException;
use Domain\Exception\EntityNotFoundException;
use Domain\Exchange\Entity\ExchangeInterface;
use Domain\Exchange\Repository\ExchangeRepositoryInterface;
use Domain\Exchange\ValueObject\ExchangeId;

class ExchangeRepository implements ExchangeRepositoryInterface
{
	/**
	 * @var ExchangeInterface[]
	 */
	private $exchanges;

	public function __construct(array $exchanges = [])
	{
		foreach ($exchanges as $exchange) {
			if (!$exchange instanceof ExchangeInterface) {
				throw new DomainException('Invalid Exchange');
			}
			if (isset($this->exchanges[(string)$exchange->getId()])) {
				throw new DomainException(sprintf('Exchange with id %s already exist in repository', $exchange->getId()));
			}
			$this->exchanges[(string)$exchange->getId()] = $exchange;
		}
	}

	/**
	 * @param ExchangeId $exchangeId
	 * @return ExchangeInterface
	 * @throws EntityNotFoundException
	 */
	public function findById(ExchangeId $exchangeId): ExchangeInterface
	{
		$exchange = $this->exchanges[(string)$exchangeId] ?? null;
		if ($exchange === null) {
			throw new EntityNotFoundException(sprintf('Exchange with id %s not found', $exchangeId));
		}
		return $exchange;
	}

	/**
	 * @return ExchangeInterface[]
	 */
	public function findAll(): array
	{
		return $this->exchanges;
	}
}