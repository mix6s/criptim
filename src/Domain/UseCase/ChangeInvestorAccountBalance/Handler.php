<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 07.01.2018
 * Time: 19:53
 */

namespace Domain\UseCase\ChangeInvestorAccountBalance;



use Domain\Entity\InvestorAccountTransaction;
use Domain\Exception\DomainException;
use Domain\Repository\InvestorAccountRepositoryInterface;
use Domain\Repository\InvestorAccountTransactionRepositoryInterface;
use Domain\ValueObject\BitMoney;

class Handler
{
	/**
	 * @var InvestorAccountTransactionRepositoryInterface
	 */
	private $investorAccountTransactionRepository;
	/**
	 * @var InvestorAccountRepositoryInterface
	 */
	private $investorAccountRepository;

	public function __construct(
		InvestorAccountTransactionRepositoryInterface $investorAccountTransactionRepository,
		InvestorAccountRepositoryInterface $investorAccountRepository
	)
	{
		$this->investorAccountTransactionRepository = $investorAccountTransactionRepository;
		$this->investorAccountRepository = $investorAccountRepository;
	}

	public function handle(Request $request): Response
	{
		$transaction = $this->investorAccountTransactionRepository->findById($request->getAccountTransactionId());
		if ($transaction->isExecuted()) {
			throw new DomainException('Transaction already executed');
		}
		$account = $this->investorAccountRepository->findById($transaction->getAccountId());
		if (!$transaction->getBitMoney()->getCurrency()->equals($account->getBalance()->getCurrency())) {
			throw new DomainException(sprintf(
				'Transaction currency %s does not equals account currency %s',
				$transaction->getBitMoney()->getCurrency(),
				$account->getBalance()->getCurrency()
			));
		}
		switch ($transaction->getType()) {
			case InvestorAccountTransaction::TYPE_DEPOSIT:
				$account->add($transaction->getBitMoney());
				break;
			default:
				throw new DomainException(sprintf('Unknown transaction type %s', $transaction->getType()));
				break;
		}
		$transaction->markAsExecuted($account->getBalance());
		return new Response();
	}
}