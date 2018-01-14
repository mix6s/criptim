<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 14.01.2018
 * Time: 13:30
 */

namespace Domain\Repository;


use Domain\Entity\User;
use Domain\Exception\EntityNotFoundException;
use Domain\ValueObject\UserId;

interface UserRepositoryInterface
{
	public function save(User $user);

	/**
	 * @param UserId $userId
	 * @return User
	 * @throws EntityNotFoundException
	 */
	public function findById(UserId $userId): User;
}