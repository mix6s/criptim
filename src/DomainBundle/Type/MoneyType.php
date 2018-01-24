<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/16/18
 * Time: 4:20 PM
 */

namespace DomainBundle\Type;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;
use Money\Currency;
use Money\Money;

class MoneyType extends JsonType
{
	const TYPE = 'money';

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
		return new Money($data['amount'], new Currency($data['currency']));
	}

	public function convertToDatabaseValue($value, AbstractPlatform $platform)
	{
		if ($value === null) {
			return null;
		}

		/** @var Money $value */
		return parent::convertToDatabaseValue(
			[
				'amount' => $value->getAmount(),
				'currency' => (string)$value->getCurrency(),
			],
			$platform
		);
	}

	public function getName()
	{
		return self::TYPE;
	}
}