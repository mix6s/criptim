<?php


namespace DomainBundle\Exchange\Policy;


use Domain\Exchange\Repository\UserExchangeAccountRepositoryInterface;
use Domain\Exchange\Repository\UserExchangeAccountTransactionRepositoryInterface;
use Domain\ValueObject\UserId;
use Money\Currency;
use Money\Money;

class ProfileData
{

	private $userExchangeAccountRepository;
	private $userExchangeAccountTransactionRepository;

	public function __construct(
		UserExchangeAccountRepositoryInterface $userExchangeAccountRepository,
		UserExchangeAccountTransactionRepositoryInterface $userExchangeAccountTransactionRepository
	)
	{
		$this->userExchangeAccountRepository = $userExchangeAccountRepository;
		$this->userExchangeAccountTransactionRepository = $userExchangeAccountTransactionRepository;
	}

	public function getBalanceMoneyByUserId(UserId $userId): Money
	{
		$balance = new Money(0, new Currency('BTC'));

		$userExchangeAccounts = $this->userExchangeAccountRepository->findByUserId($userId);

		foreach ($userExchangeAccounts as $userExchangeAccount) {
			$balance = $balance->add($userExchangeAccount->getBalance());
		}

		return $balance;
	}

	public function getDepositsMoneyByUserId(UserId $userId): Money
	{
		$deposits = new Money(0, new Currency('BTC'));

		$userExchangeAccountTransactionDeposits = $this
			->userExchangeAccountTransactionRepository
			->findByUserIdType($userId, 'deposit');

		foreach ($userExchangeAccountTransactionDeposits as $userExchangeAccountTransactionDeposit) {
			$deposits = $deposits->add($userExchangeAccountTransactionDeposit->getMoney());
		}

		return $deposits;
	}

	public function getCashoutsMoneyByUserId(UserId $userId): Money
	{
		$cashouts = new Money(0, new Currency('BTC'));

		$userExchangeAccountTransactionCashouts = $this
			->userExchangeAccountTransactionRepository
			->findByUserIdType($userId, 'cashout');

		foreach ($userExchangeAccountTransactionCashouts as $userExchangeAccountTransactionCashout) {
			$cashouts = $cashouts->add($userExchangeAccountTransactionCashout->getMoney());
		}

		return $cashouts;

	}
}