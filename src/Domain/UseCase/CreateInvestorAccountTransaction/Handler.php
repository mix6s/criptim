<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 07.01.2018
 * Time: 22:28
 */

namespace Domain\UseCase\CreateInvestorAccountTransaction;


use Domain\Entity\InvestorAccountTransaction;
use Domain\Factory\InvestorAccountTransactionIdentityFactoryInterface;
use Domain\Repository\InvestorAccountTransactionRepositoryInterface;
use Domain\UseCase;

class Handler
{
	/**
	 * @var InvestorAccountTransactionIdentityFactoryInterface
	 */
	private $transactionIdentityFactory;
	/**
	 * @var UseCase\FindInvestorAccount\Handler
	 */
	private $findInvestorAccountHandler;
	/**
	 * @var InvestorAccountTransactionRepositoryInterface
	 */
	private $accountTransactionRepository;

	public function __construct(
		InvestorAccountTransactionIdentityFactoryInterface $transactionIdentityFactory,
		UseCase\FindInvestorAccount\Handler $findInvestorAccountHandler,
		InvestorAccountTransactionRepositoryInterface $accountTransactionRepository
	)
	{

		$this->transactionIdentityFactory = $transactionIdentityFactory;
		$this->findInvestorAccountHandler = $findInvestorAccountHandler;
		$this->accountTransactionRepository = $accountTransactionRepository;
	}

	public function handle(Request $request): Response
	{
		$account = $this->findInvestorAccountHandler->handle(new UseCase\FindInvestorAccount\Request(
			$request->getInvestorId(),
			$request->getBitMoney()->getCurrency()
		))->getInvestorAccount();

		$investorAccountTransactionId = $this->transactionIdentityFactory->getNextId();
		$transaction = new InvestorAccountTransaction(
			$investorAccountTransactionId,
			$account->getId(),
			InvestorAccountTransaction::TYPE_DEPOSIT,
			$request->getBitMoney(),
			$account->getMainBalance(),
            $account->getTradingBalance()
		);
		$this->accountTransactionRepository->save($transaction);
		return new Response($transaction);
	}
}