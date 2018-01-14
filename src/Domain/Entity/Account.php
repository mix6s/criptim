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
	 * @var Currency
	 */
	protected $currency;
	/**
	 * @var Money
	 */
	protected $balance;

	public function __construct(Currency $currency)
	{
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
			$this->balance = $this->balance->add($money->absolute());
		} elseif ($money->isNegative()) {
			$this->balance = $this->balance->subtract($money->absolute());
		}
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
	public function getBalance(): Money
	{
		return $this->balance;
	}
}