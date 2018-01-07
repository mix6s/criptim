<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 07.01.2018
 * Time: 21:01
 */

namespace Domain\Factory;


use Domain\ValueObject\InvestorAccountIdentity;

interface InvestorAccountIdentityFactoryInterface
{
	public function getNextId(): InvestorAccountIdentity;
}