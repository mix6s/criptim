<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 12.01.2018
 * Time: 17:28
 */

namespace Domain\Entity;


use Domain\Exception\DomainException;
use Money\Currency;
use Money\Money;

abstract class Account
{
	/**
	 * @var AccountId
	 */
	private $accountId;
	/**
	 * @var Currency
	 */
	private $currency;
	/**
	 * @var Money
	 */
	private $balance;

	public function __construct(AccountId $accountId, Currency $currency)
	{
		$this->accountId = $accountId;
		$this->currency = $currency;
		$this->balance = new Money(0, $currency);
	}

	public function change(Money $money)
	{
		if (!$this->currency->equals($money->getCurrency())) {
			throw new DomainException(sprintf('Balance currency %s does not equals money currency %s', $this->currency,
				$money->getCurrency()));
		}
		if ($money->isPositive()) {
			$this->balance->add($money->absolute());
		} elseif ($money->isNegative()) {
			$this->balance->subtract($money->absolute());
		}
	}

}