<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 05.01.2018
 * Time: 23:49
 */

namespace Domain\Tests\Factory;


use Domain\Factory\InvestorIdentityFactoryInterface;
use Domain\ValueObject\InvestorIdentity;

class InvestorIdentityFactory implements InvestorIdentityFactoryInterface
{
	private $id = 0;

	/**
	 * @return InvestorIdentity
	 */
	public function getNextId(): InvestorIdentity
	{
		$this->id++;
		return new InvestorIdentity((string)$this->id);
	}
}