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
	private $mainBalance;
    /**
     * @var BitMoney
     */
    private $tradingBalance;

    /**
     * InvestorAccount constructor.
     * @param InvestorAccountIdentity $accountId
     * @param InvestorIdentity $investorId
     * @param BitMoney $mainBalance
     * @param BitMoney $tradingBalance
     * @throws DomainException
     */
    public function __construct(
	    InvestorAccountIdentity $accountId,
        InvestorIdentity $investorId,
        BitMoney $mainBalance,
        BitMoney $tradingBalance
    )
	{
		$this->id = $accountId;
		$this->investorId = $investorId;
		if (!$tradingBalance->getCurrency()->equals($mainBalance->getCurrency())) {
		    throw new DomainException('Main balance and trading balance must be the same currency');
        }
		$this->mainBalance = $mainBalance;
        $this->tradingBalance = $tradingBalance;
    }

	/**
	 * @return BitMoney
	 */
	public function getBalance(): BitMoney
	{
		return new BitMoney($this->mainBalance->getMoney()->add($this->tradingBalance->getMoney()));
	}

	public function addToMain(BitMoney $bitMoney)
	{
		$this->mainBalance = new BitMoney($this->mainBalance->getMoney()->add($bitMoney->getMoney()));
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

    /**
     * @return BitMoney
     */
    public function getMainBalance(): BitMoney
    {
        return $this->mainBalance;
    }

    /**
     * @return BitMoney
     */
    public function getTradingBalance(): BitMoney
    {
        return $this->tradingBalance;
    }
}