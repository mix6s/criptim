<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 07.01.2018
 * Time: 1:48
 */

namespace Domain\Policy;


use Domain\Exception\DomainException;
use Money\Currencies;
use Money\Currency;

class DepositCurrenciesPolicy implements Currencies
{
	const RUR = 'RUR';
	const SUPPORT_CURRENCIES = [
		self::RUR => 2
	];

	private static $currencies = null;

	private static function getSupportCurrencies(): array
	{
		if (self::$currencies === null) {
			self::$currencies = [];
			foreach (self::SUPPORT_CURRENCIES as $code => $subunit) {
				self::$currencies[$code] = new Currency($code);
			}
		}
		return self::$currencies;
	}
	/**
	 * {@inheritdoc}
	 */
	public function contains(Currency $currency)
	{
		return array_key_exists($currency->getCode(), self::getSupportCurrencies());
	}

	/**
	 * {@inheritdoc}
	 */
	public function subunitFor(Currency $currency)
	{
		if (!$this->contains($currency)) {
			throw new DomainException(
				'Unknown deposit currency: ' . $currency->getCode()
			);
		}
		return self::SUPPORT_CURRENCIES[$currency->getCode()];
	}

	/**
	 * {@inheritdoc}
	 */
	public function getIterator()
	{
		return new \ArrayIterator(self::getSupportCurrencies());
	}
}