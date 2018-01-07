<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 07.01.2018
 * Time: 20:05
 */

namespace Domain\Repository;


use Domain\Entity\InvestorAccountTransaction;
use Domain\Exception\EntityNotFoundException;
use Domain\ValueObject\InvestorAccountTransactionIdentity;

interface InvestorAccountTransactionRepositoryInterface
{
	public function save(InvestorAccountTransaction $accountTransaction);

	/**
	 * @param InvestorAccountTransactionIdentity $transactionId
	 * @return InvestorAccountTransaction
	 * @throws EntityNotFoundException
	 */
	public function findById(InvestorAccountTransactionIdentity $transactionId): InvestorAccountTransaction;
}