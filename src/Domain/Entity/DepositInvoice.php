<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 05.01.2018
 * Time: 22:53
 */

namespace Domain\Entity;


use Domain\ValueObject\BillingInvoice;
use Domain\ValueObject\BitMoney;
use Domain\ValueObject\DepositInvoiceIdentity;
use Domain\ValueObject\DepositMoney;
use Domain\ValueObject\DepositPayMethod;
use Domain\ValueObject\InvestorIdentity;
use Money\Money;

class DepositInvoice
{
	const STATUS_OPEN = 'open';
	const STATUS_PAYED = 'payed';

	/**
	 * @var DepositInvoiceIdentity
	 */
	private $depositInvoiceId;
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
	/**
	 * @var string
	 */
	private $status;
	/**
	 * @var null|BillingInvoice
	 */
	private $billingInvoice;
	/**
	 * @var null|BitMoney
	 */
	private $addedBitMoney;

	public function __construct(
		DepositInvoiceIdentity $depositInvoiceId,
		InvestorIdentity $investorId,
		DepositMoney $invoiceSum,
		DepositPayMethod $payMethod
	) {
		$this->depositInvoiceId = $depositInvoiceId;
		$this->investorId = $investorId;
		$this->invoiceSum = $invoiceSum;
		$this->payMethod = $payMethod;
		$this->status = self::STATUS_OPEN;
		$this->billingInvoice = null;
		$this->addedBitMoney = null;
	}

	/**
	 * @return DepositInvoiceIdentity
	 */
	public function getId(): DepositInvoiceIdentity
	{
		return $this->depositInvoiceId;
	}

	/**
	 * @return string
	 */
	public function getStatus(): string
	{
		return $this->status;
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

	/**
	 * @return InvestorIdentity
	 */
	public function getInvestorId(): InvestorIdentity
	{
		return $this->investorId;
	}

	/**
	 * @param BillingInvoice $billingInvoice
	 * @param BitMoney $bitMoneyToAdd
	 */
	public function markAsPayed(BillingInvoice $billingInvoice, BitMoney $bitMoneyToAdd)
	{
		$this->addedBitMoney = $bitMoneyToAdd;
		$this->status = self::STATUS_PAYED;
		$this->billingInvoice = $billingInvoice;
	}

	/**
	 * @return BitMoney|null
	 */
	public function getAddedBitMoney()
	{
		return $this->addedBitMoney;
	}
}