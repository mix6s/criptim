<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 06.01.2018
 * Time: 23:46
 */

namespace Domain\ValueObject;


use Domain\Exception\DomainException;
use Domain\Policy\CryptoCurrenciesPolicy;
use Money\Currency;
use Money\Money;

class BitMoney
{
	/**
	 * @var Money
	 */
	private $money;

	public function __construct(Money $money)
	{
		if (!$money->getCurrency()->isAvailableWithin(new CryptoCurrenciesPolicy())) {
			throw new DomainException(
				sprintf('Currency %s does not supported by BitMoney', $money->getCurrency()->getCode())
			);
		}
		$this->money = $money;
	}

	/**
	 * @return Money
	 */
	public function getMoney(): Money
	{
		return $this->money;
	}

	public function getCurrency(): Currency
	{
		return $this->getMoney()->getCurrency();
	}
}