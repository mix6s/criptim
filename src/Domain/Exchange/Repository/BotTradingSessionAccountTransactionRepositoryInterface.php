<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 14.01.2018
 * Time: 19:30
 */

namespace Domain\Exchange\Repository;


use Domain\Exception\EntityNotFoundException;
use Domain\Exchange\Entity\BotTradingSessionAccountTransaction;
use Domain\Exchange\ValueObject\BotTradingSessionId;
use Money\Currency;

interface BotTradingSessionAccountTransactionRepositoryInterface
{
	/**
	 * @param BotTradingSessionAccountTransaction $transaction
	 */
	public function save(BotTradingSessionAccountTransaction $transaction);

	/**
	 * @param BotTradingSessionId $sessionId
	 * @param Currency $currency
	 * @param \DateTimeImmutable $dt
	 * @return BotTradingSessionAccountTransaction
	 * @throws EntityNotFoundException
	 */
	public function findLastBySessionIdCurrencyDate(
		BotTradingSessionId $sessionId,
		Currency $currency,
		\DateTimeImmutable $dt
	): BotTradingSessionAccountTransaction;
}