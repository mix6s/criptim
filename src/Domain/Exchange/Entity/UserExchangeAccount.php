<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 13.01.2018
 * Time: 19:23
 */

namespace Domain\Exchange\Entity;


use Domain\Exchange\ValueObject\ExchangeId;
use Domain\ValueObject\UserId;
use Money\Currency;

class UserExchangeAccount extends ExchangeAccount
{
	/**
	 * @var UserId
	 */
	private $userId;

	public function __construct(UserId $userId, ExchangeId $exchangeId, Currency $currency)
	{
		parent::__construct($exchangeId, $currency);
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