<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 15.01.2018
 * Time: 19:33
 */

namespace DomainBundle\Type;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Domain\Exchange\ValueObject\BotTradingSessionId;

class BotTradingSessionIdType extends Type
{
	const TYPE = 'botTradingSessionId';

	public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
	{
		return $platform->getBigIntTypeDeclarationSQL($fieldDeclaration);
	}

	public function convertToPHPValue($value, AbstractPlatform $platform)
	{
		return new BotTradingSessionId((string)$value);
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