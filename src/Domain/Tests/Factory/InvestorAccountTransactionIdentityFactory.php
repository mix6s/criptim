<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 07.01.2018
 * Time: 22:58
 */

namespace Domain\Tests\Factory;


use Domain\Factory\InvestorAccountTransactionIdentityFactoryInterface;
use Domain\ValueObject\InvestorAccountTransactionIdentity;

class InvestorAccountTransactionIdentityFactory implements InvestorAccountTransactionIdentityFactoryInterface
{
	private $id = 0;

	public function getNextId(): InvestorAccountTransactionIdentity
	{
		$this->id++;
		return new InvestorAccountTransactionIdentity((string)$this->id);
	}
}