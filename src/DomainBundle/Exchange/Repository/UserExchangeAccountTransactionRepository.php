<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/17/18
 * Time: 5:45 PM
 */

namespace DomainBundle\Exchange\Repository;


use Doctrine\ORM\EntityRepository;
use Domain\Exchange\Entity\UserExchangeAccountTransaction;
use Domain\Exchange\Repository\UserExchangeAccountTransactionRepositoryInterface;
use Domain\Exchange\ValueObject\ExchangeId;
use Money\Currency;

class UserExchangeAccountTransactionRepository extends EntityRepository implements UserExchangeAccountTransactionRepositoryInterface
{

	public function save(UserExchangeAccountTransaction $transaction)
	{
		// TODO: Implement save() method.
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

	}
}