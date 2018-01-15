<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/8/18
 * Time: 4:32 PM
 */

namespace DomainBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Domain\Entity\User;
use Domain\Exception\EntityNotFoundException;
use Domain\Repository\UserRepositoryInterface;
use Domain\ValueObject\UserId;

class UserRepository extends EntityRepository implements UserRepositoryInterface
{


	public function findById(UserId $userId): User
	{
		/** @var User $user */
		$user = $this->find($userId);
		if (empty($user)) {
			throw new EntityNotFoundException(sprintf('User with id %d not found', (string)$userId));
		}
		return $user;
	}

	/**
	 * @param User $user
	 */
	public function save(User $user)
	{
		$this->getEntityManager()->persist($user);
		$this->getEntityManager()->flush($user);
	}
}