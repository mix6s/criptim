<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 07.01.2018
 * Time: 19:55
 */

namespace Domain\UseCase\ChangeInvestorAccountBalance;


use Domain\ValueObject\InvestorAccountTransactionIdentity;

class Request
{
	/**
	 * @var InvestorAccountTransactionIdentity
	 */
	private $accountTransactionId;

	public function __construct(InvestorAccountTransactionIdentity $accountTransactionId)
	{
		$this->accountTransactionId = $accountTransactionId;
	}

	/**
	 * @return InvestorAccountTransactionIdentity
	 */
	public function getAccountTransactionId(): InvestorAccountTransactionIdentity
	{
		return $this->accountTransactionId;
	}
}