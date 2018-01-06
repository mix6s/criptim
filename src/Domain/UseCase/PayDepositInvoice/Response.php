<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 06.01.2018
 * Time: 21:22
 */

namespace Domain\UseCase\PayDepositInvoice;


use Domain\Entity\DepositInvoice;

class Response
{
	/**
	 * @var DepositInvoice
	 */
	private $depositInvoice;

	public function __construct(DepositInvoice $depositInvoice)
	{
		$this->depositInvoice = $depositInvoice;
	}

	/**
	 * @return DepositInvoice
	 */
	public function getDepositInvoice(): DepositInvoice
	{
		return $this->depositInvoice;
	}
}