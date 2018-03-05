<?php


namespace Domain\UseCase\Response;


use Domain\Entity\UserAccount;

class GetUserAccountResponse
{
	/**
	 * @var UserAccount
	 */
	private $userAccount;

	public function __construct(UserAccount $userAccount)
	{
		$this->userAccount = $userAccount;
	}

	/**
	 * @return UserAccount
	 */
	public function getUserAccount(): UserAccount
	{
		return $this->userAccount;
	}
}