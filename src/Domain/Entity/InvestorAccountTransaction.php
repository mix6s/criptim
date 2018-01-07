<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 07.01.2018
 * Time: 18:20
 */

namespace Domain\Entity;


use Domain\ValueObject\BitMoney;
use Domain\ValueObject\InvestorAccountIdentity;
use Domain\ValueObject\InvestorAccountTransactionIdentity;

class InvestorAccountTransaction
{
	const STATUS_NEW = 'new';
	const STATUS_EXEC = 'executed';

	const TYPE_DEPOSIT = 'deposit';

	/**
	 * @var InvestorAccountTransactionIdentity
	 */
	private $id;
	/**
	 * @var string
	 */
	private $type;
	/**
	 * @var BitMoney
	 */
	private $bitMoney;
	/**
	 * @var BitMoney
	 */
	private $balance;
	/**
	 * @var string
	 */
	private $status;
	/**
	 * @var InvestorAccountIdentity
	 */
	private $accountId;
	/**
	 * @var \DateTimeImmutable
	 */
	private $createdAt;
	/**
	 * @var \DateTimeImmutable|null
	 */
	private $executedAt;

	public function __construct(
		InvestorAccountTransactionIdentity $id,
		InvestorAccountIdentity $accountId,
		string $type,
		BitMoney $bitMoney,
		BitMoney $balance
	)
	{
		$this->id = $id;
		$this->type = $type;
		$this->createdAt = new \DateTimeImmutable();
		$this->bitMoney = $bitMoney;
		$this->balance = $balance;
		$this->status = self::STATUS_NEW;
		$this->accountId = $accountId;
	}

	public function isExecuted(): bool
	{
		return $this->status === self::STATUS_EXEC;
	}

	public function markAsExecuted(BitMoney $balance)
	{
		$this->status = self::STATUS_EXEC;
		$this->balance = $balance;
		$this->executedAt = new \DateTimeImmutable();
	}

	/**
	 * @return BitMoney
	 */
	public function getBitMoney(): BitMoney
	{
		return $this->bitMoney;
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return $this->type;
	}

	/**
	 * @return InvestorAccountIdentity
	 */
	public function getAccountId(): InvestorAccountIdentity
	{
		return $this->accountId;
	}

	/**
	 * @return InvestorAccountTransactionIdentity
	 */
	public function getId(): InvestorAccountTransactionIdentity
	{
		return $this->id;
	}
}