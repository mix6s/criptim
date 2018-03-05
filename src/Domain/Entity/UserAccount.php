<?php


namespace Domain\Entity;


use Domain\ValueObject\UserId;
use Money\Currency;

class UserAccount extends Account
{
	/**
	 * @var UserId
	 */
	private $userId;

	public function __construct(UserId $userId, Currency $currency)
	{
		parent::__construct($currency);
		$this->userId = $userId;
	}

	/**
	 * @return UserId
	 */
	public function getUserId(): UserId
	{
		return $this->userId;
	}
}