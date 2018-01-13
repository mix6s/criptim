<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 12.01.2018
 * Time: 23:35
 */

namespace Domain\ValueObject;


use Domain\Entity\UserExchangeAccount;
use Domain\Exception\DomainException;

class UserExchangeAccountType
{
	const TYPE_MAIN = 'main';
	const TYPE_TRADING = 'trading';

	private static $types = [];
	/**
	 * @var string
	 */
	private $type;

	private function __construct(string $type)
	{
		$this->type = $type;
	}

	public static function MAIN(): UserExchangeAccountType
	{
		return self::resolve(self::TYPE_MAIN);
	}

	public static function TRADING(): UserExchangeAccountType
	{
		return self::resolve(self::TYPE_TRADING);
	}

	public static function resolve(string $type)
	{
		if (!in_array($type, [self::TYPE_MAIN, self::TYPE_TRADING])) {
			throw new DomainException(sprintf('Invalid UserExchangeAccountType %s', $type));
		}
		if (!isset(self::$types[$type])) {
			self::$types[$type] = new self($type);
		}
		return self::$types[$type];
	}
}