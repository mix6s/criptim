<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 07.01.2018
 * Time: 20:57
 */

namespace Domain\Factory;


use Domain\ValueObject\InvestorAccountTransactionIdentity;

interface InvestorAccountTransactionIdentityFactoryInterface
{
	public function getNextId(): InvestorAccountTransactionIdentity;
}