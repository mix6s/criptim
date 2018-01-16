<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 14.01.2018
 * Time: 13:02
 */

namespace Domain\Exchange\Entity;


use Domain\Exchange\ValueObject\BotTradingSessionAccountTransactionId;
use Domain\Exchange\ValueObject\BotTradingSessionId;
use Money\Currency;
use Money\Money;

class BotTradingSessionAccountTransaction
{
	const TYPE_BOT_TRANSFER = 'bot_transfer';
	/**
	 * @var BotTradingSessionAccountTransactionId
	 */
	private $id;
	/**
	 * @var Money
	 */
	private $money;
	/**
	 * @var string
	 */
	private $type;
	/**
	 * @var \DateTimeImmutable
	 */
	private $dt;
	/**
	 * @var Currency
	 */
	private $currency;
	/**
	 * @var Money
	 */
	private $balance;
	/**
	 * @var BotTradingSessionId
	 */
	private $botTradingSessionId;

	public function __construct(
		BotTradingSessionAccountTransactionId $id,
		BotTradingSessionId $botTradingSessionId,
		Currency $currency,
		Money $money,
		Money $balance,
		string $type
	) {

		$this->id = $id;
		$this->currency = $currency;
		$this->money = $money;
		$this->balance = $balance;
		$this->type = $type;
		$this->dt = new \DateTimeImmutable();
		$this->botTradingSessionId = $botTradingSessionId;
	}

	/**
	 * @return Money
	 */
	public function getBalance(): Money
	{
		return $this->balance;
	}

	/**
	 * @return BotTradingSessionAccountTransactionId
	 */
	public function getId(): BotTradingSessionAccountTransactionId
	{
		return $this->id;
	}

	/**
	 * @return Money
	 */
	public function getMoney(): Money
	{
		return $this->money;
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

	/**
	 * @return Currency
	 */
	public function getCurrency(): Currency
	{
		return $this->currency;
	}

	/**
	 * @return BotTradingSessionId
	 */
	public function getBotTradingSessionId(): BotTradingSessionId
	{
		return $this->botTradingSessionId;
	}
}