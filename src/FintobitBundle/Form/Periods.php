<?php


namespace FintobitBundle\Form;


class Periods
{
	private $now;

	private static $periodToMonthNumberMap = [
		'January' => 1,
		'February' => 2,
		'March' => 3,
		'April' => 4,
		'May' => 5,
		'June' => 6,
		'July' => 7,
		'August' => 8,
		'September' => 9,
		'October' => 10,
		'November' => 11,
		'December' => 12,
	];

	public function __construct()
	{
		$this->now = new \DateTimeImmutable();
	}

	public function getAvailablePeriods(): array
	{
		$currentMonthNumber = (int)$this->now->format('m');

		$monthNumbers = range(1, $currentMonthNumber);

		$return = [];
		foreach ($monthNumbers as $monthNumber) {
			$period = $this->getPeriodForMonthNumber($monthNumber);
			$return[$period] = $period;
		}
		return $return;
	}

	public function getMonthNumberForPeriod(string $period): int
	{
		return self::$periodToMonthNumberMap[$period];
	}

	public function getPeriodForMonthNumber(int $monthNumber): string
	{
		$reversedMap = array_flip(self::$periodToMonthNumberMap);
		return $reversedMap[$monthNumber];
	}

	public function resolvePeriodForDateTime(\DateTimeInterface $dateTime): string
	{
		return $dateTime->format('F');
	}

	public function resolveDateRangeForPeriod(string $period): array
	{
		$now = new \DateTimeImmutable('now');
		$periodMonth = $this->getMonthNumberForPeriod($period);
		$randomDayInPeriod = (new \DateTimeImmutable('now'))
			->setDate(
				$now->format('Y'),
				$periodMonth,
				15
				);
		$firstDayOfPeriodMonth = $randomDayInPeriod->modify('first day of this month 00:00:00');
		$lastDayOfPeriodMonth = $randomDayInPeriod->modify('last day of this month 00:00:00');
		return [$firstDayOfPeriodMonth, $lastDayOfPeriodMonth];
	}
}