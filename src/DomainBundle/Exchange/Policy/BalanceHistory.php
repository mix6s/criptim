<?php


namespace DomainBundle\Exchange\Policy;


use Doctrine\Common\Persistence\ManagerRegistry;
use Domain\ValueObject\UserId;
use FintobitBundle\Policy\UserMoneyFormatter;
use Money\Currency;
use Money\Money;

class BalanceHistory
{

	private $managerRegistry;
	private $userMoneyFormatter;

	public function __construct(
		ManagerRegistry $managerRegistry
	)
	{
		$this->managerRegistry = $managerRegistry;
		$this->userMoneyFormatter = new UserMoneyFormatter();
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
               date_trunc('day', :from_dt :: TIMESTAMP),
               date_trunc('day', :to_dt :: TIMESTAMP),
               '1 day' :: INTERVAL
           ) AS date
),
    transactions AS (
      SELECT
        date,
        (SELECT (balance ->> 'amount') :: FLOAT AS balance
		 FROM user_account_transaction t
		 WHERE date_trunc('day', t.dt) <= date
			   AND t.user_id = :user_id
			   AND t.currency = :currency
		 ORDER BY t.id DESC LIMIT 1) AS balance
      FROM dates
  ) SELECT
      TO_CHAR(date, 'YYYY-MM-DD')    AS date,
      TO_CHAR(date, 'DD')            AS day,
      COALESCE(balance, 0)           AS balance
    FROM transactions;
QUERY;

		/** @var \Doctrine\DBAL\Statement $statement */
		$statement = $this->managerRegistry->getConnection()->prepare($query);
		$fromDtFormat = $fromDt->format('Y-m-d H:i:s');
		$toDtFormat = $toDt->format('Y-m-d H:i:s');
		$statement->bindParam('from_dt', $fromDtFormat);
		$statement->bindParam('to_dt', $toDtFormat);
		$statement->bindParam('user_id', $userId);
		$statement->bindParam('currency', $currency);
		$statement->execute();
		$fetchedData = $statement->fetchAll();
		$return = [];
		$now = new \DateTimeImmutable('now');
		foreach ($fetchedData as $item) {
			$itemDt = new \DateTimeImmutable($item['date']);
			$money = new Money($item['balance'], new Currency($currency));

			$balance = $this->userMoneyFormatter->format($money);
			if ($itemDt > $now) {
				$balance = null;
			}
			$return[] = [
				'date' => $item['date'],
				'day' => $item['day'],
				'balance' => $balance
			];
		}
		return $return;
	}
}