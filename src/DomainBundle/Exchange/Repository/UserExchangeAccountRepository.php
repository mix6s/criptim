<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/17/18
 * Time: 5:44 PM
 */

namespace DomainBundle\Exchange\Repository;


use Doctrine\ORM\EntityRepository;
use Domain\Exception\EntityNotFoundException;
use Domain\Exchange\Entity\UserExchangeAccount;
use Domain\Exchange\Repository\UserExchangeAccountRepositoryInterface;
use Domain\Exchange\ValueObject\ExchangeId;
use Domain\ValueObject\UserId;
use Money\Currency;

class UserExchangeAccountRepository extends EntityRepository implements UserExchangeAccountRepositoryInterface
{

	/**
	 * @param UserId $userId
	 * @param ExchangeId $exchangeId
	 * @param Currency $currency
	 * @return UserExchangeAccount
	 * @throws EntityNotFoundException
	 */
	public function findByUserIdExchangeIdCurrency(
		UserId $userId,
		ExchangeId $exchangeId,
		Currency $currency
	): UserExchangeAccount {
		/** @var UserExchangeAccount $account */
		$account = $this->find([
			'userId' => $userId,
			'exchangeId' => $exchangeId,
			'currency' => $currency
		]);
		if ($account === null) {
			throw new EntityNotFoundException('UserExchangeAccount not found');
		}
		return $account;
	}

	/**
	 * @param UserId $userId
	 * @return UserExchangeAccount[]
	 */
	public function findByUserId(UserId $userId): array
	{
		return $this->findBy([
			'userId' => $userId,
		]);
	}

	public function save(UserExchangeAccount $account)
	{
		$this->getEntityManager()->persist($account);
		$this->getEntityManager()->flush($account);
	}
}