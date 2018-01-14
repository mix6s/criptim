<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 13.01.2018
 * Time: 19:29
 */

namespace Domain\Exchange\Factory;


use Domain\Exchange\ValueObject\BotExchangeAccountId;
use Domain\Exchange\ValueObject\BotExchangeAccountTransactionId;
use Domain\Exchange\ValueObject\BotId;
use Domain\Exchange\ValueObject\BotTradingSessionId;
use Domain\Exchange\ValueObject\ExchangeAccountTransactionId;
use Domain\Exchange\ValueObject\ExchangeId;
use Domain\Exchange\ValueObject\UserExchangeAccountId;
use Domain\Exchange\ValueObject\UserExchangeAccountTransactionId;

interface IdFactoryInterface
{
	public function getExchangeId(): ExchangeId;
	public function getBotId(): BotId;
	public function getBotTradingSessionId(): BotTradingSessionId;
	public function getUserExchangeAccountTransactionId(): UserExchangeAccountTransactionId;
	public function getBotExchangeAccountTransactionId(): BotExchangeAccountTransactionId;
}