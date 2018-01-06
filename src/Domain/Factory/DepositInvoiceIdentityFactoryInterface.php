<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 05.01.2018
 * Time: 23:23
 */

namespace Domain\Factory;


use Domain\ValueObject\DepositInvoiceIdentity;

interface DepositInvoiceIdentityFactoryInterface
{
	public function getNextId(): DepositInvoiceIdentity;
}