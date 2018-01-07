<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 07.01.2018
 * Time: 21:30
 */

namespace Domain\UseCase\FindInvestorAccount;


use Domain\Entity\InvestorAccount;

class Response
{
	/**
	 * @var InvestorAccount
	 */
	private $investorAccount;

	public function __construct(InvestorAccount $investorAccount)
	{
		$this->investorAccount = $investorAccount;
	}

	/**
	 * @return InvestorAccount
	 */
	public function getInvestorAccount(): InvestorAccount
	{
		return $this->investorAccount;
	}
}