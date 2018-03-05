<?php


namespace DomainBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Domain\Entity\UserAccount;
use Domain\Exception\EntityNotFoundException;
use Domain\Repository\UserAccountRepositoryInterface;
use Domain\ValueObject\UserId;
use Money\Currency;

class UserAccountRepository extends EntityRepository implements UserAccountRepositoryInterface
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
	): UserAccount {
		/** @var UserAccount $account */
		$account = $this->find([
			'userId' => $userId,
			'currency' => $currency
		]);
		if ($account === null) {
			throw new EntityNotFoundException('UserAccount not found');
		}
		return $account;
	}

	/**
	 * @param UserId $userId
	 * @return UserAccount[]
	 */
	public function findByUserId(UserId $userId): array
	{
		return $this->findBy([
			'userId' => $userId,
		]);
	}

	public function save(UserAccount $account)
	{
		$this->getEntityManager()->persist($account);
		$this->getEntityManager()->flush($account);
	}
}