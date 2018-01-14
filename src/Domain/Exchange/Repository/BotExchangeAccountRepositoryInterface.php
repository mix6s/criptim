<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 14.01.2018
 * Time: 13:57
 */

namespace Domain\Exchange\Repository;


use Domain\Exception\EntityNotFoundException;
use Domain\Exchange\Entity\UserExchangeAccount;
use Domain\Exchange\ValueObject\BotId;
use Domain\Exchange\ValueObject\ExchangeId;
use Money\Currency;

interface BotExchangeAccountRepositoryInterface
{
	/**
	 * @param BotId $botId
	 * @param ExchangeId $exchangeId
	 * @param Currency $currency
	 * @return UserExchangeAccount
	 * @throws EntityNotFoundException
	 */
	public function findByBotIdExchangeIdCurrency(
		BotId $botId,
		ExchangeId $exchangeId,
		Currency $currency
	): UserExchangeAccount;

	public function save(UserExchangeAccount $account);
}