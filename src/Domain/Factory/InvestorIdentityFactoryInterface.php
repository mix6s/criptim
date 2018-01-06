<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 05.01.2018
 * Time: 23:30
 */

namespace Domain\Factory;


use Domain\ValueObject\InvestorIdentity;

interface InvestorIdentityFactoryInterface
{
	/**
	 * @return InvestorIdentity
	 */
	public function getNextId(): InvestorIdentity;
}