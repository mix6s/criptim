<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 05.01.2018
 * Time: 22:35
 */

namespace Domain\UseCase\CreateDepositInvoice;


use Domain\ValueObject\DepositMoney;
use Domain\ValueObject\DepositPayMethod;
use Domain\ValueObject\InvestorIdentity;

class Request
{
	/**
	 * @var InvestorIdentity
	 */
	private $investorId;
	/**
	 * @var DepositMoney
	 */
	private $invoiceSum;
	/**
	 * @var DepositPayMethod
	 */
	private $payMethod;

	public function __construct(InvestorIdentity $investorId, DepositMoney $invoiceSum, DepositPayMethod $payMethod)
	{
		$this->investorId = $investorId;
		$this->invoiceSum = $invoiceSum;
		$this->payMethod = $payMethod;
	}

	/**
	 * @return InvestorIdentity
	 */
	public function getInvestorId(): InvestorIdentity
	{
		return $this->investorId;
	}

	/**
	 * @return DepositMoney
	 */
	public function getInvoiceSum(): DepositMoney
	{
		return $this->invoiceSum;
	}

	/**
	 * @return DepositPayMethod
	 */
	public function getPayMethod(): DepositPayMethod
	{
		return $this->payMethod;
	}
}