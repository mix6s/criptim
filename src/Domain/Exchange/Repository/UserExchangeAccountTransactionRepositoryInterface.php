<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 14.01.2018
 * Time: 14:20
 */

namespace Domain\Exchange\Repository;


use Domain\Exchange\Entity\UserExchangeAccountTransaction;
use Domain\Exchange\ValueObject\ExchangeId;
use Money\Currency;

interface UserExchangeAccountTransactionRepositoryInterface
{
	public function save(UserExchangeAccountTransaction $transaction);

	/**
	 * @param ExchangeId $exchangeId
	 * @param Currency $currency
	 * @param \DateTimeImmutable $dt
	 * @return UserExchangeAccountTransaction[]
	 */
	public function findLastByExchangeIdCurrencyDate(ExchangeId $exchangeId, Currency $currency, \DateTimeImmutable $dt): array;
}