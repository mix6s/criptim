<?php


namespace Domain\UseCase\Request;


use Domain\ValueObject\UserId;
use Money\Currency;

class GetUserAccountRequest
{
	/** @var  Currency */
	private $currency;
	/** @var  UserId */
	private $userId;

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