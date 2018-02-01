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

		try {
			// todo: resolve case when passed arguments causing to fail
			// should probably check for data, otherwise return null

			$currency = new Currency('BTC');
			$depositsSum = new Money(0, new Currency('BTC'));
			$depositsMultipliedPerSecondsInSystemSum = new Money(0, new Currency('BTC'));

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
				throw new \DomainException('have nothing start');
			}
			$startBalance = $lastTransactionForForDate->getBalance();
			$lastBalance = $lastTransactionForToDate->getBalance();

			$deposits = $this
				->userExchangeAccountTransactionRepository
				->findByUserIdCurrencyTypeFromDtToDt(
					$userId,
					$currency,
					'deposit',
					$fromDt,
					$toDt
				);

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
				->multiply(100)
			;

			$periodSeconds = $toDt->getTimestamp() - $fromDt->getTimestamp();

			$startBalanceMultipliedByPeriodSeconds = $startBalance->multiply($periodSeconds);
			$denominator = $depositsMultipliedPerSecondsInSystemSum->add($startBalanceMultipliedByPeriodSeconds);

			return $numerator->ratioOf($denominator);

		} catch (\Exception $e) {
			return 0.0;
		}

	}
}