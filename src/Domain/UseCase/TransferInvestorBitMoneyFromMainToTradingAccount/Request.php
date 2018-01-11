<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 10.01.18
 * Time: 22:02
 */

namespace Domain\UseCase\TransferInvestorBitMoneyFromMainToTradingAccount;


use Domain\ValueObject\InvestorAccountIdentity;

class Request
{
	/**
	 * @var InvestorAccountIdentity
	 */
	private $investorAccountId;

	public function __construct(InvestorAccountIdentity $investorAccountId)
	{
		$this->investorAccountId = $investorAccountId;
	}

	/**
	 * @return InvestorAccountIdentity
	 */
	public function getInvestorAccountId(): InvestorAccountIdentity
	{
		return $this->investorAccountId;
	}
}