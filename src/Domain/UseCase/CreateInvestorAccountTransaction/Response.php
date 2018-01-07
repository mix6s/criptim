<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 07.01.2018
 * Time: 22:38
 */

namespace Domain\UseCase\CreateInvestorAccountTransaction;


use Domain\Entity\InvestorAccountTransaction;

class Response
{
	/**
	 * @var InvestorAccountTransaction
	 */
	private $transaction;

	public function __construct(InvestorAccountTransaction $transaction)
	{
		$this->transaction = $transaction;
	}

	/**
	 * @return InvestorAccountTransaction
	 */
	public function getTransaction(): InvestorAccountTransaction
	{
		return $this->transaction;
	}
}