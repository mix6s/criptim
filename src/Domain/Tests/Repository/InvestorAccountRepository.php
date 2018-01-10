<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 07.01.2018
 * Time: 22:46
 */

namespace Domain\Tests\Repository;


use Domain\Entity\InvestorAccount;
use Domain\Exception\EntityNotFoundException;
use Domain\Repository\InvestorAccountRepositoryInterface;
use Domain\ValueObject\InvestorAccountIdentity;
use Domain\ValueObject\InvestorIdentity;
use Money\Currency;

class InvestorAccountRepository implements InvestorAccountRepositoryInterface
{
	use InMemoryRepositoryTrait;

	public function save(InvestorAccount $account)
	{
		$this->storeEntity($account, $account->getId());
	}

	/**
	 * @param InvestorAccountIdentity $accountId
	 * @return InvestorAccount
	 * @throws EntityNotFoundException
	 */
	public function findById(InvestorAccountIdentity $accountId): InvestorAccount
	{
		return $this->getEntity($accountId);
	}

	/**
	 * @param InvestorIdentity $investorId
	 * @param Currency $currency
	 * @return InvestorAccount
	 * @throws EntityNotFoundException
	 */
	public function findByInvestorIdAndCurrency(InvestorIdentity $investorId, Currency $currency): InvestorAccount
	{
		$items = array_filter($this->store, function (InvestorAccount $account) use ($currency, $investorId) {
			return $account->getBalance()->getCurrency()->equals($currency) && $account->getInvestorId()->equals($investorId);
		});
		if (count($items) === 0) {
			throw new EntityNotFoundException();
		}
		return array_shift($items);
	}
}