<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 12.01.2018
 * Time: 17:37
 */

namespace Domain\Entity;


use Money\Currency;

class UserAccount extends Account
{
	/**
	 * @var UserId
	 */
	private $userId;

	public function __construct(UserAccountId $accountId, UserId $userId, Currency $currency)
	{
		parent::__construct($accountId, $currency);
		$this->userId = $userId;
	}
}