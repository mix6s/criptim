<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 07.01.2018
 * Time: 21:30
 */

namespace Domain\UseCase\FindInvestorAccount;


use Domain\ValueObject\InvestorIdentity;
use Money\Currency;

class Request
{
	/**
	 * @var InvestorIdentity
	 */
	private $investorId;
	/**
	 * @var Currency
	 */
	private $currency;

	public function __construct(InvestorIdentity $investorId, Currency $currency)
	{
		$this->investorId = $investorId;
		$this->currency = $currency;
	}

	/**
	 * @return InvestorIdentity
	 */
	public function getInvestorId(): InvestorIdentity
	{
		return $this->investorId;
	}

	/**
	 * @return Currency
	 */
	public function getCurrency(): Currency
	{
		return $this->currency;
	}
}