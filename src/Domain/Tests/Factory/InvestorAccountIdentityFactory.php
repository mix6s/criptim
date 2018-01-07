<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 07.01.2018
 * Time: 22:58
 */

namespace Domain\Tests\Factory;


use Domain\Factory\InvestorAccountIdentityFactoryInterface;
use Domain\ValueObject\InvestorAccountIdentity;

class InvestorAccountIdentityFactory implements InvestorAccountIdentityFactoryInterface
{
	private $id = 0;

	public function getNextId(): InvestorAccountIdentity
	{
		$this->id++;
		return new InvestorAccountIdentity((string)$this->id);
	}
}