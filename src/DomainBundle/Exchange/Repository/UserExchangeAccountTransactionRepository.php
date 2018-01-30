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