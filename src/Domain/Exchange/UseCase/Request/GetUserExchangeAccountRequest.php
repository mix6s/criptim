<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/26/18
 * Time: 1:39 PM
 */

namespace Domain\Exchange\UseCase\Request;


use Domain\ValueObject\UserId;
use Domain\Exchange\ValueObject\ExchangeId;
use Money\Currency;

class GetUserExchangeAccountRequest
{
	/** @var  Currency */
	private $currency;
	/** @var  UserId */
	private $userId;
	/** @var  ExchangeId */
	private $exchangeId;

	/**
	 * @return Currency
	 */
	public function getCurrency()
	{
		return $this->currency;
	}

	/**
	 * @param Currency $currency
	 */
	public function setCurrency(Currency $currency)
	{
		$this->currency = $currency;
	}

	/**
	 * @return ExchangeId
	 */
	public function getExchangeId()
	{
		return $this->exchangeId;
	}

	/**
	 * @param ExchangeId $exchangeId
	 */
	public function setExchangeId(ExchangeId $exchangeId)
	{
		$this->exchangeId = $exchangeId;
	}

	/**
	 * @return UserId
	 */
	public function getUserId()
	{
		return $this->userId;
	}

	/**
	 * @param UserId $userId
	 */
	public function setUserId(UserId $userId)
	{
		$this->userId = $userId;
	}

}