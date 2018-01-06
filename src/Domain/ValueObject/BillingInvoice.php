<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 07.01.2018
 * Time: 0:20
 */

namespace Domain\ValueObject;


use Money\Money;

class BillingInvoice
{
	/**
	 * @var BillingIdentity
	 */
	private $billingIdentity;
	/**
	 * @var DepositMoney
	 */
	private $depositMoney;
	/**
	 * @var Money
	 */
	private $fee;
	/**
	 * @var DepositInvoiceIdentity
	 */
	private $depositInvoiceIdentity;

	public function __construct(
		BillingIdentity $billingIdentity,
		DepositInvoiceIdentity $depositInvoiceIdentity,
		DepositMoney $depositMoney,
		Money $fee
	)
	{
		$this->billingIdentity = $billingIdentity;
		$this->depositMoney = $depositMoney;
		$this->fee = $fee;
		$this->depositInvoiceIdentity = $depositInvoiceIdentity;
	}

	/**
	 * @return BillingIdentity
	 */
	public function getBillingIdentity(): BillingIdentity
	{
		return $this->billingIdentity;
	}

	/**
	 * @return DepositMoney
	 */
	public function getDepositMoney(): DepositMoney
	{
		return $this->depositMoney;
	}

	/**
	 * @return Money
	 */
	public function getFee(): Money
	{
		return $this->fee;
	}

	/**
	 * @return DepositInvoiceIdentity
	 */
	public function getDepositInvoiceIdentity(): DepositInvoiceIdentity
	{
		return $this->depositInvoiceIdentity;
	}
}