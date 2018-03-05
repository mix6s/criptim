<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 3/5/18
 * Time: 2:18 PM
 */

namespace Domain\Repository;


use Domain\Entity\UserAccount;
use Domain\Exception\EntityNotFoundException;
use Domain\ValueObject\UserId;
use Money\Currency;

interface UserAccountRepositoryInterface
{
	/**
	 * @param UserId $userId
	 * @param Currency $currency
	 * @return UserAccount
	 * @throws EntityNotFoundException
	 */
	public function findByUserIdCurrency(
		UserId $userId,
		Currency $currency
	): UserAccount;

	/**
	 * @param UserId $userId
	 * @return UserAccount[]
	 */
	public function findByUserId(UserId $userId): array;

	public function save(UserAccount $account);
}