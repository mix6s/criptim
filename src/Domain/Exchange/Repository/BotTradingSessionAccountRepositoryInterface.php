<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 14.01.2018
 * Time: 19:08
 */

namespace Domain\Exchange\Repository;


use Domain\Exception\EntityNotFoundException;
use Domain\Exchange\Entity\BotTradingSessionAccount;
use Domain\Exchange\ValueObject\BotTradingSessionId;
use Money\Currency;

interface BotTradingSessionAccountRepositoryInterface
{
	/**
	 * @param BotTradingSessionId $botTradingSessionId
	 * @param Currency $currency
	 * @return BotTradingSessionAccount
	 * @throws EntityNotFoundException
	 */
	public function findByBotTradingSessionIdCurrency(BotTradingSessionId $botTradingSessionId, Currency $currency): BotTradingSessionAccount;

	/**
	 * @param BotTradingSessionAccount $account
	 */
	public function save(BotTradingSessionAccount $account);
}