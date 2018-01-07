<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 07.01.2018
 * Time: 20:11
 */

namespace Domain\Repository;


use Domain\Entity\InvestorAccount;
use Domain\Exception\EntityNotFoundException;
use Domain\ValueObject\InvestorAccountIdentity;
use Domain\ValueObject\InvestorIdentity;
use Money\Currency;

interface InvestorAccountRepositoryInterface
{
	public function save(InvestorAccount $account);

	/**
	 * @param InvestorAccountIdentity $accountId
	 * @return InvestorAccount
	 * @throws EntityNotFoundException
	 */
	public function findById(InvestorAccountIdentity $accountId): InvestorAccount;

	/**
	 * @param InvestorIdentity $investorId
	 * @param Currency $currency
	 * @return InvestorAccount
	 * @throws EntityNotFoundException
	 */
	public function findByInvestorIdAndCurrency(InvestorIdentity $investorId, Currency $currency): InvestorAccount;
}