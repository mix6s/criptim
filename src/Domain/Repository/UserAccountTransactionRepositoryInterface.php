<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 3/5/18
 * Time: 2:25 PM
 */

namespace Domain\Repository;


use Domain\Entity\UserAccountTransaction;
use Domain\Exception\EntityNotFoundException;
use Domain\ValueObject\UserId;
use Money\Currency;

interface UserAccountTransactionRepositoryInterface
{
	public function save(UserAccountTransaction $transaction);

	/**
	 * @param Currency $currency
	 * @param \DateTimeImmutable $dt
	 * @return UserAccountTransaction[]
	 */
	public function findLastByCurrencyAndDate(Currency $currency, \DateTimeImmutable $dt): array;

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
	): UserAccountTransaction;

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
	): array;

	/**
	 * @param UserId $userId
	 * @param string $type
	 * @param \DateTimeInterface $fromDt
	 * @param \DateTimeInterface $toDt
	 * @return UserAccountTransaction[]
	 */
	public function findByUserIdTypeFromDtToDt(
		UserId $userId,
		string $type,
		\DateTimeInterface $fromDt,
		\DateTimeInterface $toDt
	): array;

	/**
	 * @param UserId $userId
	 * @param string $type
	 * @return UserAccountTransaction[]
	 */
	public function findByUserIdType(UserId $userId, string $type): array;

	/**
	 * @param UserId $userId
	 * @param Currency $currency
	 * @return UserAccountTransaction
	 * @throws EntityNotFoundException
	 */
	public function findFirstByUserIdCurrency(
		UserId $userId,
		Currency $currency
	): UserAccountTransaction;
}