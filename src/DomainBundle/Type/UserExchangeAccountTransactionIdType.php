<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 15.01.2018
 * Time: 19:25
 */

namespace DomainBundle\Type;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Domain\Exchange\ValueObject\UserExchangeAccountTransactionId;

class UserExchangeAccountTransactionIdType extends Type
{
	const TYPE = 'userExchangeAccountTransactionId';

	public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
	{
		return $platform->getBigIntTypeDeclarationSQL($fieldDeclaration);
	}

	public function convertToPHPValue($value, AbstractPlatform $platform)
	{
		return new UserExchangeAccountTransactionId((string)$value);
	}

	public function convertToDatabaseValue($value, AbstractPlatform $platform)
	{
		return (int)$value;
	}

	public function getName()
	{
		return self::TYPE;
	}
}