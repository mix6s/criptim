<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 14.01.2018
 * Time: 17:19
 */

namespace Domain\UseCase\Response;


use Domain\ValueObject\UserId;

class CreateUserResponse
{
	/**
	 * @var UserId
	 */
	private $userId;

	public function __construct(UserId $userId)
	{
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