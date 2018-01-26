<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/26/18
 * Time: 1:42 PM
 */

namespace Domain\Exchange\UseCase\Response;


use Domain\Exchange\Entity\UserExchangeAccount;

class GetUserExchangeAccountResponse
{
	/**
	 * @var UserExchangeAccount
	 */
	private $userExchangeAccount;

	public function __construct(UserExchangeAccount $userExchangeAccount)
	{
		$this->userExchangeAccount = $userExchangeAccount;
	}

	/**
	 * @return UserExchangeAccount
	 */
	public function getUserExchangeAccount(): UserExchangeAccount
	{
		return $this->userExchangeAccount;
	}

}