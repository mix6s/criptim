<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 14.01.2018
 * Time: 15:09
 */

namespace Domain\Exchange\Policy;


use Money\Currency;
use Money\Money;

interface MoneyFromFloatPolicyInterface
{
	public function getMoney(Currency $currency, float $amount): Money;
}