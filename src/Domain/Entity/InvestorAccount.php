<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 07.01.2018
 * Time: 18:15
 */

namespace Domain\Entity;


use Domain\Exception\DomainException;
use Domain\ValueObject\BitMoney;
use Domain\ValueObject\InvestorAccountIdentity;
use Domain\ValueObject\InvestorIdentity;

class InvestorAccount
{
	/**
	 * @var InvestorAccountIdentity
	 */
	private $id;
	/**
	 * @var InvestorIdentity
	 */
	private $investorId;
	/**
	 * @var BitMoney
	 */
	private $balance;

	public function __construct(InvestorAccountIdentity $accountId, InvestorIdentity $investorId, BitMoney $balance)
	{
		$this->id = $accountId;
		$this->investorId = $investorId;
		$this->balance = $balance;
	}

	public function change(InvestorAccountTransaction $accountTransaction)
	{
		if ($accountTransaction->isExecuted()) {
			throw new DomainException('Transaction already executed');
		}
		if (!$accountTransaction->getBitMoney()->getCurrency()->equals($this->balance->getCurrency())) {
			throw new DomainException(sprintf(
				'Transaction currency %s does not equals account currency %s',
				$accountTransaction->getBitMoney()->getCurrency(),
				$this->balance->getCurrency()
			));
		}

	}

	/**
	 * @return BitMoney
	 */
	public function getBalance(): BitMoney
	{
		return $this->balance;
	}

	public function add(BitMoney $bitMoney)
	{
		$this->balance = new BitMoney($this->balance->getMoney()->add($bitMoney->getMoney()));
	}

	/**
	 * @return InvestorAccountIdentity
	 */
	public function getId(): InvestorAccountIdentity
	{
		return $this->id;
	}

	/**
	 * @return InvestorIdentity
	 */
	public function getInvestorId(): InvestorIdentity
	{
		return $this->investorId;
	}
}