<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 13.01.2018
 * Time: 19:29
 */

namespace Domain\Exchange\Factory;


use Domain\Exchange\ValueObject\BotExchangeAccountTransactionId;
use Domain\Exchange\ValueObject\BotId;
use Domain\Exchange\ValueObject\BotTradingSessionAccountTransactionId;
use Domain\Exchange\ValueObject\BotTradingSessionId;
use Domain\Exchange\ValueObject\LocalToBittrexExchangeOrderId;
use Domain\Exchange\ValueObject\OrderId;
use Domain\Exchange\ValueObject\UserExchangeAccountTransactionId;
use Domain\ValueObject\UserAccountTransactionId;

interface IdFactoryInterface
{
	public function getBotId(): BotId;
	public function getBotTradingSessionId(): BotTradingSessionId;
	public function getUserExchangeAccountTransactionId(): UserExchangeAccountTransactionId;
	public function getUserAccountTransactionId(): UserAccountTransactionId;
	public function getBotExchangeAccountTransactionId(): BotExchangeAccountTransactionId;
	public function getBotTradingSessionAccountTransactionId(): BotTradingSessionAccountTransactionId;
	public function getOrderId(): OrderId;
	public function getLocalToBittrexExchangeOrderId(): LocalToBittrexExchangeOrderId;
}