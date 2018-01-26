<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 14.01.2018
 * Time: 17:19
 */

namespace Domain\UseCase\Response;


use Domain\Entity\User;

class CreateUserResponse
{
	/**
	 * @var User
	 */
	private $user;

	public function __construct(User $user)
	{
		$this->user = $user;
	}

	/**
	 * @return User
	 */
	public function getUser(): User
	{
		return $this->user;
	}

}