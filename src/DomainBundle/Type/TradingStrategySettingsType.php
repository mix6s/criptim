<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/16/18
 * Time: 4:52 PM
 */

namespace DomainBundle\Type;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonArrayType;
use Domain\Exchange\ValueObject\TradingStrategySettings;

class TradingStrategySettingsType extends JsonArrayType
{
	const TYPE = 'tradingStrategySettings';

	public function convertToPHPValue($value, AbstractPlatform $platform)
	{
		if ($value === null) {
			return null;
		}
		$data = parent::convertToPHPValue($value,$platform);
		return new TradingStrategySettings($data);
	}

	public function convertToDatabaseValue($value, AbstractPlatform $platform)
	{
		if ($value === null) {
			return null;
		}

		/** @var TradingStrategySettings $value */
		return parent::convertToDatabaseValue(
			$value->getData(),
			$platform
		);
	}

	public function getName()
	{
		return self::TYPE;
	}
}