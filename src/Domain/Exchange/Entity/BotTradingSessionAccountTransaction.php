<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 14.01.2018
 * Time: 13:02
 */

namespace Domain\Exchange\Entity;


use Domain\Exchange\ValueObject\BotTradingSessionAccountTransactionId;
use Money\Currency;
use Money\Money;

class BotTradingSessionAccountTransaction
{
	const TYPE_DEPOSIT = 'deposit';
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

	public function __construct(
		BotTradingSessionAccountTransactionId $id,
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
	}
}