<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/16/18
 * Time: 4:15 PM
 */

namespace DomainBundle\Type;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;
use Doctrine\DBAL\Types\Type;
use Money\Currency;
use Money\CurrencyPair;

class CurrencyPairType extends JsonType
{
	const TYPE = 'currencyPair';

	public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
	{
		return 'JSONB';
	}

	public function convertToPHPValue($value, AbstractPlatform $platform)
	{
		if ($value === null) {
			return null;
		}
		$data = parent::convertToPHPValue($value,$platform);
		return new CurrencyPair(new Currency($data['base']), new Currency($data['quote']), 0);
	}

	public function convertToDatabaseValue($value, AbstractPlatform $platform)
	{
		if ($value === null) {
			return null;
		}

		/** @var CurrencyPair $value */
		return parent::convertToDatabaseValue(
			[
				'base' => $value->getBaseCurrency()->getCode(),
				'quote' => $value->getCounterCurrency()->getCode(),
			],
			$platform
		);
	}

	public function getName()
	{
		return self::TYPE;
	}
}