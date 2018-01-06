<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 06.01.2018
 * Time: 23:48
 */

namespace Domain\ValueObject;


use Money\Money;

class DepositMoney
{
	/**
	 * @var Money
	 */
	private $money;

	public function __construct(Money $money)
	{
		$this->money = $money;
	}

	/**
	 * @return Money
	 */
	public function getMoney(): Money
	{
		return $this->money;
	}
}