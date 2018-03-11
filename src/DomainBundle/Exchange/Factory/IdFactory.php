<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/25/18
 * Time: 11:29 AM
 */

namespace DomainBundle\Exchange\Factory;


use Doctrine\DBAL\Connection;
use Domain\Exchange\Factory\IdFactoryInterface;
use Domain\Exchange\ValueObject\BotExchangeAccountTransactionId;
use Domain\Exchange\ValueObject\BotId;
use Domain\Exchange\ValueObject\BotTradingSessionAccountTransactionId;
use Domain\Exchange\ValueObject\BotTradingSessionId;
use Domain\Exchange\ValueObject\LocalToBittrexExchangeOrderId;
use Domain\Exchange\ValueObject\OrderId;
use Domain\Exchange\ValueObject\UserExchangeAccountTransactionId;
use Domain\ValueObject\UserAccountTransactionId;

class IdFactory implements IdFactoryInterface
{
	/**
	 * @var Connection
	 */
	private $connection;

	public function __construct(Connection $connection)
	{
		$this->connection = $connection;
	}

	public function getBotId(): BotId
	{
		return new BotId($this->getNextId('bot_id_seq'));
	}

	public function getBotTradingSessionId(): BotTradingSessionId
	{
		return new BotTradingSessionId($this->getNextId('bot_trading_session_id_seq'));
	}

	public function getUserExchangeAccountTransactionId(): UserExchangeAccountTransactionId
	{
		return new UserExchangeAccountTransactionId($this->getNextId('user_exchange_account_transaction_id_seq'));
	}

	public function getBotExchangeAccountTransactionId(): BotExchangeAccountTransactionId
	{
		return new BotExchangeAccountTransactionId($this->getNextId('bot_exchange_account_transaction_id_seq'));
	}

	public function getBotTradingSessionAccountTransactionId(): BotTradingSessionAccountTransactionId
	{
		return new BotTradingSessionAccountTransactionId($this->getNextId('bot_trading_session_account_transaction_id_seq'));
	}

	public function getOrderId(): OrderId
	{
		return new OrderId($this->getNextId('order_id_seq'));
	}

	public function getLocalToBittrexExchangeOrderId(): LocalToBittrexExchangeOrderId
	{
		return new LocalToBittrexExchangeOrderId($this->getNextId('local_to_bittrex_exchange_order_id_seq'));
	}

	private function getNextId(string $sequence)
	{
		return $this->connection->query(sprintf("select nextval('%s')", $sequence))->fetchColumn(0);
	}

	public function getUserAccountTransactionId(): UserAccountTransactionId
	{
		return new UserAccountTransactionId($this->getNextId('user_account_transaction_id_seq'));
	}
}