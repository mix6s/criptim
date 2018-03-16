<?php


namespace DomainBundle\Exchange\Policy;


use Domain\Exception\EntityNotFoundException;
use Domain\ValueObject\UserId;
use DomainBundle\Repository\UserAccountTransactionRepository;
use Money\Currency;
use Money\Money;

class AnyMomentUserBalanceResolver
{
	/**
	 * @var UserAccountTransactionRepository
	 */
	private $userAccountTransactionRepository;

	public function __construct(
		UserAccountTransactionRepository $userAccountTransactionRepository
	)
	{
		$this->userAccountTransactionRepository = $userAccountTransactionRepository;
	}

	public function getBalanceByUserIdCurrencyDt(
		UserId $userId,
		Currency $currency,
		\DateTimeInterface $dt
	): Money
	{
		try {
			$lastTransactionForToDate = $this
				->userAccountTransactionRepository
				->findLastByUserIdCurrencyDate(
					$userId,
					$currency,
					$dt
				);
			$balance = $lastTransactionForToDate->getBalance();
		} catch (EntityNotFoundException $exception) {
			$balance = new Money(0, $currency);
		}

		return $balance;
	}
}