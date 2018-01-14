<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 14.01.2018
 * Time: 14:20
 */

namespace Domain\Exchange\Repository;


use Domain\Exchange\Entity\BotExchangeAccountTransaction;

interface BotExchangeAccountTransactionRepositoryInterface
{
	public function save(BotExchangeAccountTransaction $transaction);
}