<?php


namespace DomainBundle\Exchange\Policy;


use Domain\Exchange\Repository\UserExchangeAccountTransactionRepositoryInterface;
use Domain\ValueObject\UserId;
use Money\Currency;
use Money\Money;

class ProfitabilityCalculator
{

	private $userExchangeAccountTransactionRepository;

	public function __construct(
		UserExchangeAccountTransactionRepositoryInterface $userExchangeAccountTransactionRepository
	)
	{
		$this->userExchangeAccountTransactionRepository = $userExchangeAccountTransactionRepository;
		$this->formatter = new CryptoMoneyFormatter();
	}

	public function getProfitabilityByUserIdFromDtToDt(
		UserId $userId,
		Currency $currency,
		\DateTimeInterface $fromDt,
		\DateTimeInterface $toDt
	): float
	{
		$firstDt = null;
		$now = new \DateTimeImmutable();
		if ($toDt->getTimestamp() > $now->getTimestamp()) {
			$toDt = $now;
		}

		$denominator = new Money(0, $currency);

		$lastTransactionsByExchangeForToDate = $this
			->userExchangeAccountTransactionRepository
			->findLastByUserIdCurrencyDate(
				$userId,
				$currency,
				$toDt
			);

		$endBalance = new Money(0, $currency);
		foreach ($lastTransactionsByExchangeForToDate as $transaction) {
			$endBalance = $endBalance->add($transaction->getBalance());
		}

		$numerator = $endBalance;
		$lastTransactionsByExchangeForFromDate = $this
			->userExchangeAccountTransactionRepository
			->findLastByUserIdCurrencyDate(
				$userId,
				$currency,
				$fromDt
			);

		foreach ($lastTransactionsByExchangeForFromDate as $transaction) {
			if ($firstDt === null) {
				$firstDt = $transaction->getDt();
			}
			$numerator = $numerator->subtract($transaction->getBalance());
			$denominator = $denominator->add($transaction->getBalance()->multiply($toDt->getTimestamp() - $transaction->getDt()->getTimestamp()));
			if ($transaction->getDt()->getTimestamp() < $firstDt->getTimestamp()) {
				$firstDt = $transaction->getDt();
			}
		}

		$depositTransactions = $this
			->userExchangeAccountTransactionRepository
			->findByUserIdCurrencyTypeFromDtToDt(
				$userId,
				$currency,
				'deposit',
				$fromDt,
				$toDt
			);
		foreach ($depositTransactions as $transaction) {
			if ($firstDt === null) {
				$firstDt = $transaction->getDt();
			}
			$numerator = $numerator->subtract($transaction->getMoney());
			$denominator = $denominator->add($transaction->getMoney()->multiply($toDt->getTimestamp() - $transaction->getDt()->getTimestamp()));
			if ($transaction->getDt()->getTimestamp() < $firstDt->getTimestamp()) {
				$firstDt = $transaction->getDt();
			}
		}
		if ($denominator->isZero()) {
			return 0;
		}
		if ($firstDt === null) {
			$firstDt = $fromDt;
		}
		$interval = $toDt->getTimestamp() - max($firstDt->getTimestamp(), $fromDt->getTimestamp());
		$result = $numerator->multiply(100)->multiply($interval)->ratioOf($denominator);
		return $result;
	}
}