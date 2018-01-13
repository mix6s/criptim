<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 12.01.2018
 * Time: 17:42
 */

namespace Domain\Entity;


use Money\Currency;

class UserExchangeAccount extends UserAccount
{
	/**
	 * @var ExchangeId
	 */
	private $exchangeId;

	public function __construct(UserExchangeAccountId $accountId, UserExchangeAccountType $type, UserId $userId, ExchangeId $exchangeId, Currency $currency)
	{
		parent::__construct($accountId, $userId, $currency);
		$this->exchangeId = $exchangeId;
	}
}