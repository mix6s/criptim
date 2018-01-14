<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 14.01.2018
 * Time: 13:28
 */

namespace Domain\Exchange\UseCase\Request;


use Domain\Exchange\ValueObject\ExchangeId;
use Domain\ValueObject\UserId;
use Money\Currency;

class UserDepositMoneyRequest
{
	/**
	 * @var UserId
	 */
	private $userId;
	/**
	 * @var ExchangeId
	 */
	private $exchangeId;
	/**
	 * @var Currency
	 */
	private $currency;
	/**
	 * @var float
	 */
	private $amount;

	public function __construct(UserId $userId, ExchangeId $exchangeId, Currency $currency, float $amount)
	{
		$this->userId = $userId;
		$this->exchangeId = $exchangeId;
		$this->currency = $currency;
		$this->amount = $amount;
	}

	/**
	 * @return UserId
	 */
	public function getUserId(): UserId
	{
		return $this->userId;
	}

	/**
	 * @return ExchangeId
	 */
	public function getExchangeId(): ExchangeId
	{
		return $this->exchangeId;
	}

	/**
	 * @return float
	 */
	public function getAmount(): float
	{
		return $this->amount;
	}

	/**
	 * @return Currency
	 */
	public function getCurrency(): Currency
	{
		return $this->currency;
	}
}