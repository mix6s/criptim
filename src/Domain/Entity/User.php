<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 14.01.2018
 * Time: 13:30
 */

namespace Domain\Entity;


use Domain\ValueObject\UserId;

class User
{
	/**
	 * @var UserId
	 */
	private $id;

	public function __construct(UserId $id)
	{
		$this->id = $id;
	}

	/**
	 * @return UserId
	 */
	public function getId(): UserId
	{
		return $this->id;
	}
}