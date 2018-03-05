<?php


namespace DomainBundle\Exchange\Policy;


use Domain\Exception\EntityNotFoundException;
use Domain\Exchange\Repository\UserExchangeAccountTransactionRepositoryInterface;
use Domain\Repository\UserAccountTransactionRepositoryInterface;
use Domain\ValueObject\UserId;
use Money\Currency;
use Money\Money;

class ProfitabilityCalculator
{
	/**
	 * @var UserAccountTransactionRepositoryInterface
	 */
	private $userAccountTransactionRepository;

	public function __construct(
		UserAccountTransactionRepositoryInterface $userAccountTransactionRepository
	)
	{
		$this->formatter = new CryptoMoneyFormatter();
		$this->userAccountTransactionRepository = $userAccountTransactionRepository;
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

		try {
			$lastTransactionForToDate = $this
				->userAccountTransactionRepository
				->findLastByUserIdCurrencyDate(
					$userId,
					$currency,
					$toDt
				);
			$endBalance = $lastTransactionForToDate->getBalance();
		} catch (EntityNotFoundException $exception) {
			$endBalance = new Money(0, $currency);
		}

		$numerator = $endBalance;

		try {
			$lastTransactionForFromDate = $this
				->userAccountTransactionRepository
				->findLastByUserIdCurrencyDate(
					$userId,
					$currency,
					$fromDt
				);
			$startBalance = $lastTransactionForFromDate->getBalance();
			$firstDt = $lastTransactionForFromDate->getDt();
		} catch (EntityNotFoundException $exception) {
			$firstDt = $fromDt;
			$startBalance = new Money(0, $currency);
		}


		$numerator = $numerator->subtract($startBalance);
		$denominator = $denominator->add($startBalance->multiply($toDt->getTimestamp() - $firstDt->getTimestamp()));

		$depositTransactions = $this
			->userAccountTransactionRepository
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