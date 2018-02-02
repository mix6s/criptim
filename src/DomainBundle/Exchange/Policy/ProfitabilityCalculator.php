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
	}

	public function getProfitabilityByUserIdFromDtToDt(
		UserId $userId,
		\DateTimeInterface $fromDt,
		\DateTimeInterface $toDt
	): float
	{
		$currency = new Currency('BTC');
		$deposits = $this
			->userExchangeAccountTransactionRepository
			->findByUserIdCurrencyTypeFromDtToDt(
				$userId,
				$currency,
				'deposit',
				$fromDt,
				$toDt
			);

		if (\count($deposits) === 0) {
			return 0;
		}
		dump($deposits);
		$depositsSum = new Money(0, $currency);
		$depositsMultipliedPerSecondsInSystemSum = new Money(0, $currency);

		$lastTransactionForForDate = $this
			->userExchangeAccountTransactionRepository
			->findLastByUserIdCurrencyDate(
				$userId,
				$currency,
				$fromDt
			);

		$lastTransactionForToDate = $this
			->userExchangeAccountTransactionRepository
			->findLastByUserIdCurrencyDate(
				$userId,
				$currency,
				$toDt
			);

		if ($lastTransactionForForDate === null || $lastTransactionForToDate === null) {
			//
			return 0;
		}
		$startBalance = $lastTransactionForForDate->getBalance();
		$lastBalance = $lastTransactionForToDate->getBalance();


		foreach ($deposits as $deposit) {
			$depositsSum = $depositsSum
				->add($deposit->getMoney());

			$depositSecondsInSystem = $toDt->getTimestamp() - $deposit->getDt()->getTimestamp();

			$depositMultipliedPerSecondsInSystem = $deposit->getMoney()->multiply($depositSecondsInSystem);

			$depositsMultipliedPerSecondsInSystemSum = $depositsMultipliedPerSecondsInSystemSum
				->add($depositMultipliedPerSecondsInSystem);
		}

		$numerator = $lastBalance
			->subtract($startBalance)
			->subtract($depositsSum)
			->multiply(100);

		$periodSeconds = $toDt->getTimestamp() - $fromDt->getTimestamp();

		$startBalanceMultipliedByPeriodSeconds = $startBalance->multiply($periodSeconds);
		$denominator = $depositsMultipliedPerSecondsInSystemSum->add($startBalanceMultipliedByPeriodSeconds);

		return $numerator->ratioOf($denominator);

	}
}