<?php


namespace DomainBundle\Exchange\Policy;


use Domain\DTO\PeriodChangeProfileDataAggregate;
use Domain\Entity\UserAccountTransaction;
use Domain\Exception\EntityNotFoundException;
use Domain\Repository\UserAccountRepositoryInterface;
use Domain\Repository\UserAccountTransactionRepositoryInterface;
use Domain\ValueObject\UserId;
use FintobitBundle\Policy\UserMoneyFormatter;
use Money\Currency;
use Money\Money;
use Money\MoneyFormatter;

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
	private $moneyFormatter;
	/**
	 * @var ProfitabilityCalculator
	 */
	private $profitabilityCalculator;
	/**
	 * @var BalanceHistory
	 */
	private $balanceHistoryPolicy;
	/**
	 * @var AnyMomentUserBalanceResolver
	 */
	private $anyMomentUserBalance;

	public function __construct(
		UserAccountRepositoryInterface $userAccountRepository,
		UserAccountTransactionRepositoryInterface $userAccountTransactionRepository,
		ProfitabilityCalculator $profitabilityCalculator,
		MoneyFormatter $moneyFormatter,
		BalanceHistory $balanceHistoryPolicy,
		AnyMomentUserBalanceResolver $anyMomentUserBalance
	)
	{
		$this->moneyFormatter = $moneyFormatter;
		$this->userAccountRepository = $userAccountRepository;
		$this->userAccountTransactionRepository = $userAccountTransactionRepository;
		$this->profitabilityCalculator = $profitabilityCalculator;
		$this->balanceHistoryPolicy = $balanceHistoryPolicy;
		$this->anyMomentUserBalance = $anyMomentUserBalance;
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
			->findByUserIdType(
				$userId,
				UserAccountTransaction::TYPE_DEPOSIT
			);

		foreach ($userExchangeAccountTransactionDeposits as $userExchangeAccountTransactionDeposit) {
			$deposits = $deposits->add($userExchangeAccountTransactionDeposit->getMoney());
		}
		return $deposits;
	}

	public function getFeeMoneyByUserId(UserId $userId): Money
	{
		$deposits = new Money(0, new Currency('BTC'));

		$userExchangeAccountTransactionDeposits = $this
			->userAccountTransactionRepository
			->findByUserIdType(
				$userId,
				UserAccountTransaction::TYPE_FEE
			);

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
			->findByUserIdType(
				$userId,
				UserAccountTransaction::TYPE_CASHOUT
			);

		foreach ($userExchangeAccountTransactionCashouts as $userExchangeAccountTransactionCashout) {
			$cashouts = $cashouts->add($userExchangeAccountTransactionCashout->getMoney());
		}

		return $cashouts;

	}

	public function getTransactionHistory(UserId $userId): array
	{
		$transactions = $this
			->userAccountTransactionRepository
			->findByUserIdType(
				$userId,
				UserAccountTransaction::TYPE_TRADING_DIFF
			);
		$return = [];
		foreach ($transactions as $transaction) {
			$return[] = [
				'isOutcome' => $transaction->getMoney()->isNegative(),
				'isIncome' => $transaction->getMoney()->isPositive(),
				'amount' => $this->moneyFormatter->format($transaction->getMoney()),
				'date' => $transaction->getDt()->format('Y-m-d H:i:s'),
				'currency' => $transaction->getCurrency()
			];
		}
		return $return;
	}

	public function getProfitabilityByUserId(UserId $userId): float
	{
		$btcCurrency = new Currency('BTC');
		try {
			$firstTransaction = $this
				->userAccountTransactionRepository
				->findFirstByUserIdCurrency(
					$userId,
					$btcCurrency
				);

		} catch (EntityNotFoundException $exception) {
			return 0.0;
		}

		$now = new \DateTimeImmutable('now');
		return $this->profitabilityCalculator->getProfitabilityByUserIdCurrencyFromDtToDt(
			$userId,
			$btcCurrency,
			$firstTransaction->getDt(),
			$now
		);
	}

	public function getProfitability(
		UserId $userId,
		\DateTimeInterface $fromDt,
		\DateTimeInterface $toDt
	): float
	{
		$btcCurrency = new Currency('BTC');
		return $this->profitabilityCalculator->getProfitabilityByUserIdCurrencyFromDtToDt(
			$userId,
			$btcCurrency,
			$fromDt,
			$toDt
		);
	}

	public function getPeriodChangeProfileDataAggregateByUserIdFromDtToDt(
		UserId $userId,
		\DateTimeInterface $fromDt,
		\DateTimeInterface $toDt
	): PeriodChangeProfileDataAggregate
	{
		$btcCurrency = new Currency('BTC');

		$depositTransactions = $this
			->userAccountTransactionRepository
			->findByUserIdTypeFromDtToDt(
				$userId,
				UserAccountTransaction::TYPE_DEPOSIT,
				$fromDt,
				$toDt
			);

		$cashoutTransactions = $this
			->userAccountTransactionRepository
			->findByUserIdTypeFromDtToDt(
				$userId,
				UserAccountTransaction::TYPE_CASHOUT,
				$fromDt,
				$toDt
			);

		$feeTransactions = $this
			->userAccountTransactionRepository
			->findByUserIdTypeFromDtToDt(
				$userId,
				UserAccountTransaction::TYPE_FEE,
				$fromDt,
				$toDt
			);

		$depositsMoney = $this->calculateMoneySumFromTransactions($depositTransactions);
		$cashoutsMoney = $this->calculateMoneySumFromTransactions($cashoutTransactions);
		$feesMoney = $this->calculateMoneySumFromTransactions($feeTransactions);

		$profitability = $this->profitabilityCalculator->getProfitabilityByUserIdCurrencyFromDtToDt(
			$userId,
			$btcCurrency,
			$fromDt,
			$toDt
		);
		$historyData = $this->balanceHistoryPolicy->fetchByUserIdCurrencyFromDtToDt(
			$userId, $btcCurrency, $fromDt, $toDt
		);

		$periodStartBalance = $this->anyMomentUserBalance->getBalanceByUserIdCurrencyDt(
			$userId,
			$btcCurrency,
			$fromDt
		);
		$periodEndBalance = $this->anyMomentUserBalance->getBalanceByUserIdCurrencyDt(
			$userId,
			$btcCurrency,
			$toDt
		);
		return new PeriodChangeProfileDataAggregate(
			$this->moneyFormatter,
			$depositsMoney,
			$cashoutsMoney,
			$feesMoney,
			$profitability,
			$periodStartBalance,
			$periodEndBalance,
			$historyData
		);
	}

	/**
	 * @param UserAccountTransaction[] $transactions
	 * @return Money
	 */
	private function calculateMoneySumFromTransactions(array $transactions): Money
	{
		$carry = new Money(0, new Currency('BTC'));
		foreach ($transactions as $transaction) {
			$carry = $carry->add($transaction->getMoney());
		}
		return $carry;
	}

	public function getPortfolioByUserId(UserId $userId): array
	{
		// todo: implement portfolio based on:
//SELECT btsa.balance
//FROM bot_trading_session_account AS btsa
//  JOIN bot_trading_session AS bts on bts.id = btsa.bot_trading_session_id
//WHERE bts.status ='active'
//
//UNION ALL
//
//SELECT balance
//FROM bot_exchange_account AS bea
//JOIN bot AS b ON b.id = bea.bot_id
//WHERE b.status = 'active'

		return [];
	}
}