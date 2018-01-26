<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 15.01.2018
 * Time: 19:32
 */

namespace DomainBundle\Type;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Domain\Exchange\ValueObject\BotTradingSessionAccountTransactionId;

class BotTradingSessionAccountTransactionIdType extends Type
{
	const TYPE = 'botTradingSessionAccountTransactionId';

	public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
	{
		return $platform->getBigIntTypeDeclarationSQL($fieldDeclaration);
	}

	public function convertToPHPValue($value, AbstractPlatform $platform)
	{
		return new BotTradingSessionAccountTransactionId((string)$value);
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