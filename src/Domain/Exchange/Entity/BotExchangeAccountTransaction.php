<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 14.01.2018
 * Time: 15:03
 */

namespace Domain\Exchange\Entity;


use Domain\Exchange\ValueObject\BotExchangeAccountTransactionId;
use Domain\Exchange\ValueObject\BotId;
use Domain\Exchange\ValueObject\ExchangeId;
use Money\Currency;
use Money\Money;

class BotExchangeAccountTransaction
{
	const TYPE_DEPOSIT = 'deposit';
	const TYPE_SESSION_TRANSFER = 'session_transfer';

	/**
	 * @var BotExchangeAccountTransactionId
	 */
	private $id;
	/**
	 * @var BotId
	 */
	private $botId;
	/**
	 * @var ExchangeId
	 */
	private $exchangeId;
	/**
	 * @var Currency
	 */
	private $currency;
	/**
	 * @var Money
	 */
	private $money;
	/**
	 * @var Money
	 */
	private $balance;
	/**
	 * @var string
	 */
	private $type;
	/**
	 * @var \DateTimeImmutable
	 */
	private $dt;

	public function __construct(
		BotExchangeAccountTransactionId $id,
		BotId $botId,
		ExchangeId $exchangeId,
		Currency $currency,
		Money $money,
		Money $balance,
		string $type
	) {
		$this->id = $id;
		$this->botId = $botId;
		$this->exchangeId = $exchangeId;
		$this->currency = $currency;
		$this->money = $money;
		$this->balance = $balance;
		$this->type = $type;
		$this->dt = new \DateTimeImmutable();
	}

	/**
	 * @return BotExchangeAccountTransactionId
	 */
	public function getId(): BotExchangeAccountTransactionId
	{
		return $this->id;
	}

	/**
	 * @return BotId
	 */
	public function getBotId(): BotId
	{
		return $this->botId;
	}

	/**
	 * @return ExchangeId
	 */
	public function getExchangeId(): ExchangeId
	{
		return $this->exchangeId;
	}

	/**
	 * @return Currency
	 */
	public function getCurrency(): Currency
	{
		return $this->currency;
	}

	/**
	 * @return Money
	 */
	public function getMoney(): Money
	{
		return $this->money;
	}

	/**
	 * @return Money
	 */
	public function getBalance(): Money
	{
		return $this->balance;
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return $this->type;
	}

	/**
	 * @return \DateTimeImmutable
	 */
	public function getDt(): \DateTimeImmutable
	{
		return $this->dt;
	}
}