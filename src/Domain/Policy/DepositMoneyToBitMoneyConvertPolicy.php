<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 07.01.2018
 * Time: 0:01
 */

namespace Domain\Policy;


use Domain\ValueObject\BitMoney;
use Domain\ValueObject\DepositMoney;

interface DepositMoneyToBitMoneyConvertPolicy
{
	public function convert(DepositMoney $depositMoney): BitMoney;
}