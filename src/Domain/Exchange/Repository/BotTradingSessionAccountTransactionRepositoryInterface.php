<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 14.01.2018
 * Time: 19:30
 */

namespace Domain\Exchange\Repository;


use Domain\Exchange\Entity\BotTradingSessionAccountTransaction;

interface BotTradingSessionAccountTransactionRepositoryInterface
{
	/**
	 * @param BotTradingSessionAccountTransaction $transaction
	 */
	public function save(BotTradingSessionAccountTransaction $transaction);
}