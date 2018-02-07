<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/17/18
 * Time: 5:45 PM
 */

namespace DomainBundle\Exchange\Repository;


use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Domain\Exchange\Entity\UserExchangeAccountTransaction;
use Domain\Exchange\Repository\UserExchangeAccountTransactionRepositoryInterface;
use Domain\Exchange\ValueObject\ExchangeId;
use Domain\ValueObject\UserId;
use DomainBundle\Type\UserExchangeAccountTransactionIdType;
use Money\Currency;

class UserExchangeAccountTransactionRepository extends EntityRepository implements UserExchangeAccountTransactionRepositoryInterface
{

	public function save(UserExchangeAccountTransaction $transaction)
	{
		$this->getEntityManager()->persist($transaction);
		$this->getEntityManager()->flush($transaction);
	}

	/**
	 * @param ExchangeId $exchangeId
	 * @param Currency $currency
	 * @param \DateTimeImmutable $dt
	 * @return UserExchangeAccountTransaction[]
	 */
	public function findLastByExchangeIdCurrencyDate(
		ExchangeId $exchangeId,
		Currency $currency,
		\DateTimeImmutable $dt
	): array {
		return $this->createNativeNamedQuery('findLastByExchangeIdCurrencyDate')
			->setParameter('currency', $currency)
			->setParameter('exchange_id', $exchangeId)
			->setParameter('dt', $dt)
			->getResult();
	}

	/**
	 * @param UserId $userId
	 * @param Currency $currency
	 * @param \DateTimeInterface $dt
	 * @return UserExchangeAccountTransaction[]
	 */
	public function findLastByUserIdCurrencyDate(
		UserId $userId,
		Currency $currency,
		\DateTimeInterface $dt
	): array
	{
		return $this->createNativeNamedQuery('findLastByUserIdCurrencyDate')
			->setParameter('user_id', $userId)
			->setParameter('currency', $currency)
			->setParameter('dt', $dt)
			->getResult();
	}

	/**
	 * @param UserId $userId
	 * @param Currency $currency
	 * @param string $type
	 * @param \DateTimeInterface $fromDt
	 * @param \DateTimeInterface $toDt
	 * @return UserExchangeAccountTransaction[]
	 */
	public function findByUserIdCurrencyTypeFromDtToDt(
		UserId $userId,
		Currency $currency,
		string $type,
		\DateTimeInterface $fromDt,
		\DateTimeInterface $toDt
	): array
	{
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
	 * @return UserExchangeAccountTransaction[]
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