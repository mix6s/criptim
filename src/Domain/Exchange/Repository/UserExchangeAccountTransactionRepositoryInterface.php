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
use Domain\ValueObject\UserId;
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
	): array;

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
	): array;

	/**
	 * @param UserId $userId
	 * @param string $type
	 * @return UserExchangeAccountTransaction[]
	 */
	public function findByUserIdType(UserId $userId, string $type): array;

}