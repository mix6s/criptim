<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 05.01.2018
 * Time: 22:38
 */

namespace Domain\UseCase\CreateDepositInvoice;


use Domain\Entity\DepositInvoice;

class Response
{
	/**
	 * @var DepositInvoice
	 */
	private $invoice;

	public function __construct(DepositInvoice $invoice)
	{
		$this->invoice = $invoice;
	}

	/**
	 * @return DepositInvoice
	 */
	public function getDepositInvoice(): DepositInvoice
	{
		return $this->invoice;
	}
}