<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 10.01.18
 * Time: 22:02
 */

namespace Domain\UseCase\TransferInvestorBitMoneyFromMainToTradingAccount;


use Domain\Repository\InvestorAccountRepositoryInterface;
use Domain\UseCase;

class Handler
{
	/**
	 * @var InvestorAccountRepositoryInterface
	 */
	private $investorAccountRepository;
	/**
	 * @var UseCase\ChangeInvestorAccountBalance\Handler
	 */
	private $changeInvestorAccountBalanceHandler;

	public function __construct(
		InvestorAccountRepositoryInterface $investorAccountRepository,
		UseCase\ChangeInvestorAccountBalance\Handler $changeInvestorAccountBalanceHandler
	)
	{

		$this->investorAccountRepository = $investorAccountRepository;
		$this->changeInvestorAccountBalanceHandler = $changeInvestorAccountBalanceHandler;
	}

	public function handle(Request $request): Response
	{
		$accountId = $request->getInvestorAccountId();
		$this->investorAccountRepository->findById($accountId);
		$this->changeInvestorAccountBalanceHandler->handle(
			new UseCase\ChangeInvestorAccountBalance\Request()
		);
		return new Response();
	}
}