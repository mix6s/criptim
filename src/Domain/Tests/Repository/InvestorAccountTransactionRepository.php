<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 07.01.2018
 * Time: 22:52
 */

namespace Domain\Tests\Repository;


use Domain\Entity\InvestorAccountTransaction;
use Domain\Exception\EntityNotFoundException;
use Domain\Repository\InvestorAccountTransactionRepositoryInterface;
use Domain\ValueObject\InvestorAccountTransactionIdentity;

class InvestorAccountTransactionRepository implements InvestorAccountTransactionRepositoryInterface
{
	use InMemoryRepositoryTrait;

	public function save(InvestorAccountTransaction $accountTransaction)
	{
		$this->storeEntity($accountTransaction, $accountTransaction->getId());
	}

	/**
	 * @param InvestorAccountTransactionIdentity $transactionId
	 * @return InvestorAccountTransaction
	 * @throws EntityNotFoundException
	 */
	public function findById(InvestorAccountTransactionIdentity $transactionId): InvestorAccountTransaction
	{
		return $this->getEntity($transactionId);
	}
}