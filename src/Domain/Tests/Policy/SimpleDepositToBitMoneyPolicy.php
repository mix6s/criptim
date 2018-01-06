<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 07.01.2018
 * Time: 1:42
 */

namespace Domain\Tests\Policy;


use Domain\Policy\DepositMoneyToBitMoneyConvertPolicy;
use Domain\ValueObject\BitMoney;
use Domain\ValueObject\DepositMoney;
use Money\Money;

class SimpleDepositToBitMoneyPolicy implements DepositMoneyToBitMoneyConvertPolicy
{

	public function convert(DepositMoney $depositMoney): BitMoney
	{
		$btc = Money::BTC($depositMoney->getMoney()->divide(1000000)->getAmount());
		return new BitMoney($btc);
	}
}