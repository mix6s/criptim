<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 10.01.18
 * Time: 22:02
 */

namespace Domain\UseCase\TransferInvestorBitMoneyFromMainToTradingAccount;


use Domain\Entity\InvestorAccountTransaction;
use Domain\Exception\DomainException;
use Domain\Repository\InvestorAccountTransactionRepositoryInterface;
use Domain\UseCase;

class Handler
{
	/**
	 * @var UseCase\ChangeInvestorAccountBalance\Handler
	 */
	private $changeInvestorAccountBalanceHandler;
	/**
	 * @var InvestorAccountTransactionRepositoryInterface
	 */
	private $investorAccountTransactionRepository;

	public function __construct(
		InvestorAccountTransactionRepositoryInterface $investorAccountTransactionRepository,
		UseCase\ChangeInvestorAccountBalance\Handler $changeInvestorAccountBalanceHandler
	) {
		$this->changeInvestorAccountBalanceHandler = $changeInvestorAccountBalanceHandler;
		$this->investorAccountTransactionRepository = $investorAccountTransactionRepository;
	}

	public function handle(Request $request): Response
	{
		$transactionId = $request->getInvestorAccountTransactionId();
		$transaction = $this->investorAccountTransactionRepository->findById($transactionId);
		if ($transaction->getType() !== InvestorAccountTransaction::TYPE_TO_TRADING) {
			throw new DomainException(
				sprintf(
					'Not available transaction type "%s" for transfer bit money to trading account',
					$transaction->getType()
				)
			);
		}
		$this->changeInvestorAccountBalanceHandler->handle(
			new UseCase\ChangeInvestorAccountBalance\Request($transactionId)
		);
		return new Response();
	}
}