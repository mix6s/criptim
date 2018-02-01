<?php


namespace DomainBundle\Exchange\Policy;


use Doctrine\Common\Persistence\ManagerRegistry;
use Domain\Policy\DomainCurrenciesPolicy;
use Domain\ValueObject\UserId;
use Money\Currency;

class BalanceHistory
{

	private $managerRegistry;
	private $domainCurrenciesPolicy;

	public function __construct(
		ManagerRegistry $managerRegistry,
		DomainCurrenciesPolicy $domainCurrenciesPolicy
	)
	{
		$this->managerRegistry = $managerRegistry;
		$this->domainCurrenciesPolicy = $domainCurrenciesPolicy;
	}

	public function fetchByUserIdCurrencyFromDtToDt(
		UserId $userId,
		Currency $currency,
		\DateTimeInterface $fromDt,
		\DateTimeInterface $toDt
	): array
	{
		$query = <<<QUERY
WITH dates AS (
    SELECT generate_series(
               :from_dt :: TIMESTAMP,
               :to_dt :: TIMESTAMP,
               '1 day' :: INTERVAL
           ) AS date
), transactions AS (
    SELECT
      date,
      (SELECT balance
       FROM user_exchange_account_transaction t
       WHERE t.dt < date
             AND t.user_id = :user_id
             AND t.currency = :currency
       ORDER BY t.dt DESC
       LIMIT 1) AS balance
    FROM dates
) SELECT
    TO_CHAR(date, 'YYYY-MM-DD') AS date,
    COALESCE((balance ->> 'amount') :: FLOAT / (10 ^ :subunit), 0) as balance
  FROM transactions;
QUERY;
		/** @var \Doctrine\DBAL\Statement $statement */
		$statement = $this->managerRegistry->getConnection()->prepare($query);
		$fromDtFormat = $fromDt->format('Y-m-d H:i:s');
		$toDtFormat = $toDt->format('Y-m-d H:i:s');
		$subunit = $this->domainCurrenciesPolicy->subunitFor($currency);
		$statement->bindParam('from_dt', $fromDtFormat);
		$statement->bindParam('to_dt', $toDtFormat);
		$statement->bindParam('user_id', $userId);
		$statement->bindParam('subunit', $subunit);
		$statement->bindParam('currency', $currency);
		$statement->execute();
		return $statement->fetchAll();
	}
}