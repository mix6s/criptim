<?php


namespace Domain\DTO;


use FintobitBundle\Policy\UserMoneyFormatter;
use Money\Money;
use Money\MoneyFormatter;

class PeriodChangeProfileDataAggregate implements \JsonSerializable
{

	private $moneyFormatter;

	/**
	 * @var Money
	 */
	private $depositsMoney;
	/**
	 * @var Money
	 */
	private $cashoutsMoney;
	/**
	 * @var Money
	 */
	private $feeMoney;
	/**
	 * @var float
	 */
	private $profitability;
	/**
	 * @var array
	 */
	private $balanceHistory;

	/**
	 * @var Money
	 */
	private $periodStartBalance;
	/**
	 * @var Money
	 */
	private $periodEndBalance;

	public function __construct(
		MoneyFormatter $moneyFormatter,
		Money $depositsMoney,
		Money $cashoutsMoney,
		Money $feeMoney,
		float $profitability,
		Money $periodStartBalance,
		Money $periodEndBalance,
		array $balanceHistory
	)
	{
		$this->depositsMoney = $depositsMoney;
		$this->cashoutsMoney = $cashoutsMoney;
		$this->feeMoney = $feeMoney;
		$this->profitability = $profitability;
		$this->balanceHistory = $balanceHistory;
		$this->periodStartBalance = $periodStartBalance;
		$this->periodEndBalance = $periodEndBalance;
		$this->moneyFormatter = $moneyFormatter;
	}

	/**
	 * @return Money
	 */
	public function getDepositsMoney(): Money
	{
		return $this->depositsMoney;
	}

	/**
	 * @return Money
	 */
	public function getCashoutsMoney(): Money
	{
		return $this->cashoutsMoney;
	}

	/**
	 * @return Money
	 */
	public function getFeeMoney(): Money
	{
		return $this->feeMoney;
	}

	/**
	 * @return float
	 */
	public function getProfitability(): float
	{
		return $this->profitability;
	}


	/**
	 * @return array
	 */
	public function getBalanceHistory(): array
	{
		return $this->balanceHistory;
	}

	/**
	 * Specify data which should be serialized to JSON
	 * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
	 * @return mixed data which can be serialized by <b>json_encode</b>,
	 * which is a value of any type other than a resource.
	 * @since 5.4.0
	 */
	public function jsonSerialize()
	{
		return [
			'depositsMoney' => vsprintf('%s %s', [
				$this->moneyFormatter->format($this->depositsMoney),
				$this->depositsMoney->getCurrency()
			]),
			'cashoutsMoney' => vsprintf('%s %s', [
				$this->moneyFormatter->format($this->cashoutsMoney),
				$this->cashoutsMoney->getCurrency()
			]),
			'feesMoney' => vsprintf('%s %s', [
				$this->moneyFormatter->format($this->feeMoney),
				$this->feeMoney->getCurrency()
			]),
			'profitability' => [
				'format' => number_format($this->profitability, 2) . '%',
				'isPositive' => $this->profitability > 0,
				'isNegative' => $this->profitability < 0
			],
			'periodStartBalance' => vsprintf('%s %s', [
				$this->moneyFormatter->format($this->periodStartBalance),
				$this->periodStartBalance->getCurrency()
			]),
			'periodEndBalance' => vsprintf('%s %s', [
				$this->moneyFormatter->format($this->periodEndBalance),
				$this->periodEndBalance->getCurrency()
			]),
			'balanceHistory' => $this->balanceHistory
		];
	}
}