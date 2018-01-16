<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 15.01.2018
 * Time: 19:30
 */

namespace DomainBundle\Type;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Domain\Exchange\ValueObject\ExchangeId;

class ExchangeIdType extends Type
{
	const TYPE = 'exchangeId';

	public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
	{
		return $platform->getVarcharTypeDeclarationSQL($fieldDeclaration);
	}

	public function convertToPHPValue($value, AbstractPlatform $platform)
	{
		return new ExchangeId((string)$value);
	}

	public function convertToDatabaseValue($value, AbstractPlatform $platform)
	{
		return (string)$value;
	}

	public function getName()
	{
		return self::TYPE;
	}
}