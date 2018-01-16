<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/16/18
 * Time: 4:15 PM
 */

namespace DomainBundle\Type;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Money\Currency;

class CurrencyType extends Type
{
	const TYPE = 'currency';

	public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
	{
		return $platform->getVarcharTypeDeclarationSQL($fieldDeclaration);
	}

	public function convertToPHPValue($value, AbstractPlatform $platform)
	{
		if ($value === null) {
			return null;
		}

		return new Currency((string)$value);
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