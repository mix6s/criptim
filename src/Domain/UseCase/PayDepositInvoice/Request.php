<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 06.01.2018
 * Time: 21:23
 */

namespace Domain\UseCase\PayDepositInvoice;


use Domain\ValueObject\BillingInvoice;

class Request
{
	/**
	 * @var BillingInvoice
	 */
	private $billingInvoice;

	public function __construct(BillingInvoice $billingInvoice)
	{
		$this->billingInvoice = $billingInvoice;
	}

	/**
	 * @return BillingInvoice
	 */
	public function getBillingInvoice(): BillingInvoice
	{
		return $this->billingInvoice;
	}
}