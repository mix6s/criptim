<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 07.01.2018
 * Time: 22:29
 */

namespace Domain\UseCase\CreateInvestorAccountTransaction;


use Domain\ValueObject\BitMoney;
use Domain\ValueObject\InvestorIdentity;

class Request
{
	/**
	 * @var string
	 */
	private $type;
	/**
	 * @var BitMoney
	 */
	private $bitMoney;
	/**
	 * @var InvestorIdentity
	 */
	private $investorId;

	public function __construct(InvestorIdentity $investorId, string $type, BitMoney $bitMoney)
	{
		$this->type = $type;
		$this->bitMoney = $bitMoney;
		$this->investorId = $investorId;
	}


	/**
	 * @return string
	 */
	public function getType(): string
	{
		return $this->type;
	}

	/**
	 * @return BitMoney
	 */
	public function getBitMoney(): BitMoney
	{
		return $this->bitMoney;
	}

	/**
	 * @return InvestorIdentity
	 */
	public function getInvestorId(): InvestorIdentity
	{
		return $this->investorId;
	}
}