<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 14.01.2018
 * Time: 13:23
 */

namespace Domain\Factory;


use Domain\ValueObject\UserId;

interface UserIdFactoryInterface
{
	public function getUserId(): UserId;
}