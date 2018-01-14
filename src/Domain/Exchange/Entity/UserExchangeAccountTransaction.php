<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 14.01.2018
 * Time: 14:13
 */

namespace Domain\Exchange\Entity;


use Domain\Exchange\ValueObject\UserExchangeAccountTransactionId;
use Domain\Exchange\ValueObject\ExchangeId;
use Domain\ValueObject\UserId;
use Money\Currency;
use Money\Money;

class UserExchangeAccountTransaction
{
	const TYPE_DEPOSIT = 'deposit';

	/**
	 * @var UserExchangeAccountTransactionId
	 */
	private $id;
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
		UserExchangeAccountTransactionId $id,
		UserId $userId,
		ExchangeId $exchangeId,
		Currency $currency,
		Money $money,
		Money $balance,
		string $type
	) {
		$this->id = $id;
		$this->exchangeId = $exchangeId;
		$this->currency = $currency;
		$this->money = $money;
		$this->balance = $balance;
		$this->userId = $userId;
		$this->type = $type;
		$this->dt = new \DateTimeImmutable();
	}
}