<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 05.01.2018
 * Time: 22:48
 */

namespace Domain\Repository;


use Domain\Entity\Investor;
use Domain\Exception\EntityNotFoundException;
use Domain\ValueObject\InvestorIdentity;

interface InvestorRepositoryInterface
{
	/**
	 * @param InvestorIdentity $investorIdentity
	 * @throws EntityNotFoundException
	 * @return Investor
	 */
	public function findById(InvestorIdentity $investorIdentity);

	/**
	 * @param Investor $investor
	 */
	public function save(Investor $investor);
}