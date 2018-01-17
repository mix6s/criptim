<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/17/18
 * Time: 5:42 PM
 */

namespace DomainBundle\Exchange\Repository;


use Domain\Exception\EntityNotFoundException;
use Domain\Exchange\Entity\ExchangeInterface;
use Domain\Exchange\Repository\ExchangeRepositoryInterface;
use Domain\Exchange\ValueObject\ExchangeId;

class ExchangeRepository implements ExchangeRepositoryInterface
{

	public function save(ExchangeInterface $exchange)
	{
	}

	/**
	 * @param ExchangeId $exchangeId
	 * @return ExchangeInterface
	 * @throws EntityNotFoundException
	 */
	public function findById(ExchangeId $exchangeId): ExchangeInterface
	{
	}
}