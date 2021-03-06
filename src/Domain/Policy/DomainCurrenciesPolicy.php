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
	const XRP = 'XRP';
	const DASH = 'DASH';
	const ZEC = 'ZEC';
	const XMR = 'XMR';
	const BCH = 'BCH';
	const LTC = 'LTC';
	const TRX = 'TRX';
	const ETC = 'ETC';
	const EOS = 'EOS';
	const XDN = 'XDN';
	const XEM = 'XEM';
	const NXT = 'NXT';
	const BCN = 'BCN';
	const BTG = 'BTG';
	const NEO = 'NEO';
	const LSK = 'LSK';

	const SUPPORT_CURRENCIES = [
		self::BTC => 8,
		self::ETH => 8,
		self::BNT => 8,
		self::XRP => 8,
		self::DASH => 8,
		self::ZEC => 8,
		self::XMR => 8,
		self::BCH => 8,
		self::LTC => 8,
		self::TRX => 8,
		self::ETC => 8,
		self::EOS => 8,
		self::XDN => 8,
		self::XEM => 8,
		self::NXT => 8,
		self::BCN => 8,
		self::BTG => 8,
		self::NEO => 8,
		self::LSK => 8,
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