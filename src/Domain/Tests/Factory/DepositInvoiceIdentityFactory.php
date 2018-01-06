<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 06.01.2018
 * Time: 0:12
 */

namespace Domain\Tests\Factory;


use Domain\Factory\DepositInvoiceIdentityFactoryInterface;
use Domain\ValueObject\DepositInvoiceIdentity;

class DepositInvoiceIdentityFactory implements DepositInvoiceIdentityFactoryInterface
{
	private $id = 0;

	/**
	 * @return DepositInvoiceIdentity
	 */
	public function getNextId(): DepositInvoiceIdentity
	{
		return new DepositInvoiceIdentity((string)$this->id++);
	}
}