<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 07.01.2018
 * Time: 21:30
 */

namespace Domain\UseCase\FindInvestorAccount;


use Domain\Entity\InvestorAccount;
use Domain\Exception\EntityNotFoundException;
use Domain\Factory\InvestorAccountIdentityFactoryInterface;
use Domain\Policy\CryptoCurrenciesPolicy;
use Domain\Repository\InvestorAccountRepositoryInterface;
use Domain\ValueObject\BitMoney;
use Money\Money;

class Handler
{
	/**
	 * @var InvestorAccountRepositoryInterface
	 */
	private $accountRepository;
	/**
	 * @var InvestorAccountIdentityFactoryInterface
	 */
	private $accountIdentityFactory;

	public function __construct(
		InvestorAccountRepositoryInterface $accountRepository,
		InvestorAccountIdentityFactoryInterface $accountIdentityFactory
	)
	{
		$this->accountRepository = $accountRepository;
		$this->accountIdentityFactory = $accountIdentityFactory;
	}

	public function handle(Request $request): Response
	{
		if ($request->getCurrency()->isAvailableWithin(new CryptoCurrenciesPolicy()))
		try {
			$account = $this->accountRepository->findByInvestorIdAndCurrency(
				$request->getInvestorId(),
				$request->getCurrency()
			);
		} catch (EntityNotFoundException $e) {
			$account = new InvestorAccount(
				$this->accountIdentityFactory->getNextId(),
				$request->getInvestorId(),
				new BitMoney(new Money(0, $request->getCurrency()))
			);
			$this->accountRepository->save($account);
		}
		return new Response($account);
	}
}