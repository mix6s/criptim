<?php


namespace DomainBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Domain\Entity\UserAccountTransaction;
use Domain\Exception\EntityNotFoundException;
use Domain\Repository\UserAccountTransactionRepositoryInterface;
use Domain\ValueObject\UserId;
use Money\Currency;

class UserAccountTransactionRepository extends EntityRepository implements UserAccountTransactionRepositoryInterface
{

	public function save(UserAccountTransaction $transaction)
	{
		$this->getEntityManager()->persist($transaction);
		$this->getEntityManager()->flush($transaction);
	}

	/**
	 * @param Currency $currency
	 * @param \DateTimeImmutable $dt
	 * @return UserAccountTransaction[]
	 */
	public function findLastByCurrencyAndDate(Currency $currency, \DateTimeImmutable $dt): array
	{
		return $this->createNativeNamedQuery('findLastByCurrencyAndDate')
			->setParameter('currency', $currency)
			->setParameter('dt', $dt)
			->getResult();
	}

	/**
	 * @param UserId $userId
	 * @param Currency $currency
	 * @param \DateTimeInterface $dt
	 * @return UserAccountTransaction
	 * @throws EntityNotFoundException
	 */
	public function findLastByUserIdCurrencyDate(
		UserId $userId,
		Currency $currency,
		\DateTimeInterface $dt
	): UserAccountTransaction {
		$transaction = $this->createNativeNamedQuery('findLastByUserIdCurrencyAndDate')
			->setParameter('user_id', $userId)
			->setParameter('currency', $currency)
			->setParameter('dt', $dt)
			->getOneOrNullResult();
		if ($transaction === null) {
			throw new EntityNotFoundException('UserAccountTransaction not found');
		}
		return $transaction;
	}

	/**
	 * @param UserId $userId
	 * @param Currency $currency
	 * @param string $type
	 * @param \DateTimeInterface $fromDt
	 * @param \DateTimeInterface $toDt
	 * @return UserAccountTransaction[]
	 */
	public function findByUserIdCurrencyTypeFromDtToDt(
		UserId $userId,
		Currency $currency,
		string $type,
		\DateTimeInterface $fromDt,
		\DateTimeInterface $toDt
	): array {
		return $this->createNativeNamedQuery('findByUserIdCurrencyFromDtToDt')
			->setParameter('user_id', $userId)
			->setParameter('currency', $currency)
			->setParameter('type', $type)
			->setParameter('from_dt', $fromDt)
			->setParameter('to_dt', $toDt)
			->getResult();
	}

	/**
	 * @param UserId $userId
	 * @param string $type
	 * @return UserAccountTransaction[]
	 */
	public function findByUserIdType(UserId $userId, string $type): array
	{
		return $this->findBy(
			[
				'userId' => $userId,
				'type' => $type
			]
		);
	}
}