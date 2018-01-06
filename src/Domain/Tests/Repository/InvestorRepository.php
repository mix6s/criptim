<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 05.01.2018
 * Time: 23:36
 */

namespace Domain\Tests\Repository;


use Domain\Entity\Investor;
use Domain\Exception\EntityNotFoundException;
use Domain\Repository\InvestorRepositoryInterface;
use Domain\ValueObject\InvestorIdentity;

class InvestorRepository implements InvestorRepositoryInterface
{
	use InMemoryRepositoryTrait;

	/**
	 * @param InvestorIdentity $investorIdentity
	 * @throws EntityNotFoundException
	 * @return Investor
	 */
	public function findById(InvestorIdentity $investorIdentity)
	{
		return $this->getEntity($investorIdentity);
	}

	/**
	 * @param Investor $investor
	 */
	public function save(Investor $investor)
	{
		$this->storeEntity($investor, $investor->getId());
	}
}