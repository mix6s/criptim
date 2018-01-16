<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/8/18
 * Time: 5:54 PM
 */

namespace DomainBundle\Type;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\DBAL\Types\Type;
use Domain\ValueObject\UserId;

class DateTimeImmutableType extends DateTimeType
{
	const TYPE = 'dateTimeImmutable';

	public function convertToPHPValue($value, AbstractPlatform $platform)
	{
		if ($value === null) {
			return null;
		}
		return new \DateTimeImmutable((string)$value);
	}

	public function getName()
	{
		return self::TYPE;
	}
}