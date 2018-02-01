<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 13.01.2018
 * Time: 19:24
 */

namespace Domain\Exchange\Repository;


use Domain\Exception\EntityNotFoundException;
use Domain\Exchange\Entity\UserExchangeAccount;
use Domain\Exchange\ValueObject\ExchangeId;
use Domain\ValueObject\UserId;
use Money\Currency;

interface UserExchangeAccountRepositoryInterface
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
	): UserExchangeAccount;

	/**
	 * @param UserId $userId
	 * @return UserExchangeAccount[]
	 */
	public function findByUserId(UserId $userId): array;

	public function save(UserExchangeAccount $account);
}