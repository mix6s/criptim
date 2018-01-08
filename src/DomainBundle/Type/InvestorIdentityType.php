<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/8/18
 * Time: 5:54 PM
 */

namespace DomainBundle\Type;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Domain\ValueObject\InvestorIdentity;

class InvestorIdentityType extends Type
{
	const TYPE = 'investorId';

	public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
	{
		return $platform->getBigIntTypeDeclarationSQL($fieldDeclaration);
	}

	public function convertToPHPValue($value, AbstractPlatform $platform)
	{
		return new InvestorIdentity((string)$value);
	}

	public function convertToDatabaseValue($value, AbstractPlatform $platform)
	{
		return (int)$value;
	}

	public function getName()
	{
		return self::TYPE; // modify to match your constant name
	}
}