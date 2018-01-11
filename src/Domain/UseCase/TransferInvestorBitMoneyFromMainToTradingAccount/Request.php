<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 10.01.18
 * Time: 22:02
 */

namespace Domain\UseCase\TransferInvestorBitMoneyFromMainToTradingAccount;


use Domain\ValueObject\InvestorAccountTransactionIdentity;

class Request
{
	/**
	 * @var InvestorAccountTransactionIdentity
	 */
	private $investorAccountTransactionId;

	public function __construct(InvestorAccountTransactionIdentity $investorAccountTransactionId)
	{
		$this->investorAccountTransactionId = $investorAccountTransactionId;
	}

	/**
	 * @return InvestorAccountTransactionIdentity
	 */
	public function getInvestorAccountTransactionId(): InvestorAccountTransactionIdentity
	{
		return $this->investorAccountTransactionId;
	}
}