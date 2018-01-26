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


	/**
	 * @param UserId $userId
	 */
	public function setUserId(UserId $userId)
	{
		$this->userId = $userId;
	}

	/**
	 * @param ExchangeId $exchangeId
	 */
	public function setExchangeId(ExchangeId $exchangeId)
	{
		$this->exchangeId = $exchangeId;
	}

	/**
	 * @param Currency $currency
	 */
	public function setCurrency(Currency $currency)
	{
		$this->currency = $currency;
	}

	/**
	 * @param float $amount
	 */
	public function setAmount(float $amount)
	{
		$this->amount = $amount;
	}

	/**
	 * @return UserId
	 */
	public function getUserId()
	{
		return $this->userId;
	}

	/**
	 * @return ExchangeId
	 */
	public function getExchangeId()
	{
		return $this->exchangeId;
	}

	/**
	 * @return float
	 */
	public function getAmount()
	{
		return $this->amount;
	}

	/**
	 * @return Currency
	 */
	public function getCurrency()
	{
		return $this->currency;
	}
}