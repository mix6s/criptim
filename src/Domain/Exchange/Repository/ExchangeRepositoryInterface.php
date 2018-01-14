<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 14.01.2018
 * Time: 13:39
 */

namespace Domain\Exchange\Repository;


use Domain\Exception\EntityNotFoundException;
use Domain\Exchange\Entity\Exchange;
use Domain\Exchange\ValueObject\ExchangeId;

interface ExchangeRepositoryInterface
{
	public function save(Exchange $exchange);

	/**
	 * @param ExchangeId $exchangeId
	 * @return Exchange
	 * @throws EntityNotFoundException
	 */
	public function findById(ExchangeId $exchangeId): Exchange;
}