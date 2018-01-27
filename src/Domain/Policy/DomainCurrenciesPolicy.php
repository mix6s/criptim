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

class DomainCurrenciesPolicy implements Currencies
{
	const DOMAIN_SUBUNIT = 4;

	const POSTFIX = '_T';

	const BTC = 'BTC';
	const ETH = 'ETH';
	const BNT = 'BNT';

	const SUPPORT_CURRENCIES = [
		self::BTC => 8,
		self::ETH => 3,
		self::BNT => 3,
	];

	protected static $currencies = null;

	protected static function getSupportCurrencies(): array
	{
		if (self::$currencies === null) {
			self::$currencies = [];
			foreach (self::SUPPORT_CURRENCIES as $code => $subunit) {
				self::$currencies[$code] = new Currency($code);
				self::$currencies[$code . self::POSTFIX] = new Currency($code . self::POSTFIX);
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
				'Unknown crypto currency: ' . $currency->getCode()
			);
		}
		$code = $currency->getCode();
		if ($this->isDomainCurrency($currency)) {
			return self::SUPPORT_CURRENCIES[$code] + self::DOMAIN_SUBUNIT;
		} else {
			return self::SUPPORT_CURRENCIES[$this->getFormattedCurrency($currency)];
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function getIterator()
	{
		return new \ArrayIterator(self::getSupportCurrencies());
	}

	public function isDomainCurrency(Currency $currency)
	{
		return strpos($currency->getCode(), self::POSTFIX) === false;
	}

	public function getFormattedCurrency(Currency $currency)
	{
		return str_replace(self::POSTFIX, '', $currency->getCode());
	}
}