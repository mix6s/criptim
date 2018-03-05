<?php


namespace DomainBundle\Exchange\Policy;


use Domain\Exchange\Repository\UserExchangeAccountRepositoryInterface;
use Domain\Exchange\Repository\UserExchangeAccountTransactionRepositoryInterface;
use Domain\Repository\UserAccountRepositoryInterface;
use Domain\Repository\UserAccountTransactionRepositoryInterface;
use Domain\ValueObject\UserId;
use Money\Currency;
use Money\Money;

class ProfileData
{

	/**
	 * @var UserAccountRepositoryInterface
	 */
	private $userAccountRepository;
	/**
	 * @var UserAccountTransactionRepositoryInterface
	 */
	private $userAccountTransactionRepository;

	public function __construct(
		UserAccountRepositoryInterface $userAccountRepository,
		UserAccountTransactionRepositoryInterface $userAccountTransactionRepository
	)
	{

		$this->userAccountRepository = $userAccountRepository;
		$this->userAccountTransactionRepository = $userAccountTransactionRepository;
	}

	public function getBalanceMoneyByUserId(UserId $userId): Money
	{
		$btcCurrency = new Currency('BTC');
		$balance = new Money(0, $btcCurrency);

		$userAccounts = $this->userAccountRepository->findByUserId($userId);

		foreach ($userAccounts as $userAccount) {
			if (!$userAccount->getCurrency()->equals($btcCurrency)) {
				continue;
			}
			$balance = $balance->add($userAccount->getBalance());
		}

		return $balance;
	}

	public function getDepositsMoneyByUserId(UserId $userId): Money
	{
		$deposits = new Money(0, new Currency('BTC'));

		$userExchangeAccountTransactionDeposits = $this
			->userAccountTransactionRepository
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
			->userAccountTransactionRepository
			->findByUserIdType($userId, 'cashout');

		foreach ($userExchangeAccountTransactionCashouts as $userExchangeAccountTransactionCashout) {
			$cashouts = $cashouts->add($userExchangeAccountTransactionCashout->getMoney());
		}

		return $cashouts;

	}
}