<?php


namespace Domain\Entity;


use Domain\ValueObject\UserAccountTransactionId;
use Domain\ValueObject\UserId;
use Money\Currency;
use Money\Money;

class UserAccountTransaction
{
	const TYPE_DEPOSIT = 'deposit';
	const TYPE_TRADING_DIFF = 'trading_diff';

	/**
	 * @var UserAccountTransactionId
	 */
	private $id;
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
	 * @var UserId
	 */
	private $userId;
	/**
	 * @var string
	 */
	private $type;
	/**
	 * @var \DateTimeImmutable
	 */
	private $dt;

	public function __construct(
		UserAccountTransactionId $id,
		UserId $userId,
		Currency $currency,
		Money $money,
		Money $balance,
		string $type,
		\DateTimeImmutable $dt = null
	) {
		$this->id = $id;
		$this->currency = $currency;
		$this->money = $money;
		$this->balance = $balance;
		$this->userId = $userId;
		$this->type = $type;
		$this->dt = $dt ?? new \DateTimeImmutable();
	}

	/**
	 * @return Money
	 */
	public function getBalance(): Money
	{
		return $this->balance;
	}

	/**
	 * @return UserId
	 */
	public function getUserId(): UserId
	{
		return $this->userId;
	}

	/**
	 * @return UserAccountTransactionId
	 */
	public function getId(): UserAccountTransactionId
	{
		return $this->id;
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