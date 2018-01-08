<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/8/18
 * Time: 4:32 PM
 */

namespace DomainBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Domain\Entity\Investor;
use Domain\Exception\EntityNotFoundException;
use Domain\Repository\InvestorRepositoryInterface;
use Domain\ValueObject\InvestorIdentity;

class InvestorRepository extends EntityRepository implements InvestorRepositoryInterface
{

	/**
	 * @param InvestorIdentity $investorIdentity
	 * @throws EntityNotFoundException
	 * @return Investor
	 */
	public function findById(InvestorIdentity $investorIdentity)
	{
		/** @var Investor $game */
		$game = $this->find($investorIdentity);
		if (empty($game)) {
			throw new EntityNotFoundException(sprintf('Game with id %d not found', $id));
		}
		return $game;
	}

	/**
	 * @param Investor $investor
	 */
	public function save(Investor $investor)
	{
		$this->getEntityManager()->persist($investor);
		$this->getEntityManager()->flush($investor);
	}
}