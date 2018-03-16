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
	/**
	 * @var AnyMomentUserBalanceResolver
	 */
	private $anyMomentUserBalanceResolver;

	public function __construct(
		UserAccountTransactionRepositoryInterface $userAccountTransactionRepository,
		AnyMomentUserBalanceResolver $anyMomentUserBalanceResolver
	)
	{
		$this->userAccountTransactionRepository = $userAccountTransactionRepository;
		$this->anyMomentUserBalanceResolver = $anyMomentUserBalanceResolver;
	}

	public function getProfitabilityByUserIdCurrencyFromDtToDt(
		UserId $userId,
		Currency $currency,
		\DateTimeInterface $fromDt,
		\DateTimeInterface $toDt
	): float
	{

		try {
			$firstTransaction = $this
				->userAccountTransactionRepository
				->findFirstByUserIdCurrency(
					$userId,
					$currency
				);

			$firstTransactionDt = $firstTransaction->getDt();
			if ($firstTransactionDt > $fromDt) {
				$fromDt = $firstTransactionDt;
			}
			if ($firstTransactionDt > $toDt) {
				$toDt = $firstTransactionDt;
			}
		} catch (EntityNotFoundException $exception) {
			return 0.0;
		}

		$firstDt = null;
		$now = new \DateTimeImmutable();
		if ($toDt->getTimestamp() > $now->getTimestamp()) {
			$toDt = $now;
		}

		$denominator = new Money(0, $currency);

		$endBalance = $this->anyMomentUserBalanceResolver->getBalanceByUserIdCurrencyDt($userId, $currency, $toDt);
		$startBalance = $this->anyMomentUserBalanceResolver->getBalanceByUserIdCurrencyDt($userId, $currency, $fromDt);

		$numerator = $endBalance;

		$numerator = $numerator->subtract($startBalance);
		$denominator = $denominator->add($startBalance->multiply($toDt->getTimestamp() - $fromDt->getTimestamp()));

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
			$numerator = $numerator->subtract($transaction->getMoney());
			$denominator = $denominator->add(
				$transaction->getMoney()
					->multiply(
						$toDt->getTimestamp() - $transaction->getDt()->getTimestamp())
			);
			if ($transaction->getDt()->getTimestamp() < $fromDt->getTimestamp()) {
				$fromDt = $transaction->getDt();
			}
		}
		if ($denominator->isZero()) {
			return 0;
		}
		$interval = $toDt->getTimestamp() - $fromDt->getTimestamp();
		return $numerator->multiply(100)->multiply($interval)->ratioOf($denominator);
	}
}