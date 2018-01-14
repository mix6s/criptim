<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 14.01.2018
 * Time: 13:39
 */

namespace Domain\Exchange\Repository;


use Domain\Exception\EntityNotFoundException;
use Domain\Exchange\Entity\ExchangeInterface;
use Domain\Exchange\ValueObject\ExchangeId;

interface ExchangeRepositoryInterface
{
	public function save(ExchangeInterface $exchange);

	/**
	 * @param ExchangeId $exchangeId
	 * @return ExchangeInterface
	 * @throws EntityNotFoundException
	 */
	public function findById(ExchangeId $exchangeId): ExchangeInterface;
}