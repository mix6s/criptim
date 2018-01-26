<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/26/18
 * Time: 2:36 PM
 */

namespace Domain\Policy;


use Domain\Exception\DomainException;
use Money\Currency;
use Money\CurrencyPair;
use Money\Money;
use Money\Number;

class DomainMoneyExchangePolicy
{
	private $domainCurrencyPolicy;

	public function __construct()
	{
		$this->domainCurrencyPolicy = new DomainCurrenciesPolicy();
	}

	public function convertFromDomain(Money $money, $roundingMode = Money::ROUND_HALF_DOWN)
	{
		$baseCurrency = $money->getCurrency();
		$counterCurrency = $money->getCurrency();
		$ratio = (string) $this->quoteFromDomain($baseCurrency, $counterCurrency)->getConversionRatio();

		$baseCurrencySubunit = $this->domainCurrencyPolicy->subunitFor($baseCurrency);
		$counterCurrencySubunit = $this->domainCurrencyPolicy->subunitFor($counterCurrency);
		$subunitDifference = $baseCurrencySubunit - $counterCurrencySubunit;

		$ratio = (string) Number::fromString($ratio)->base10($subunitDifference);

		$counterValue = $money->multiply($ratio, $roundingMode);

		return new Money($counterValue->getAmount(), $counterCurrency);
	}

	public function convertToDomain(Money $money, $roundingMode = Money::ROUND_HALF_DOWN)
	{
		$baseCurrency = $money->getCurrency();
		$counterCurrency = $money->getCurrency();
		$ratio = (string) $this->quoteToDomain($baseCurrency, $counterCurrency)->getConversionRatio();

		$baseCurrencySubunit = $this->domainCurrencyPolicy->subunitFor($baseCurrency);
		$counterCurrencySubunit = $this->domainCurrencyPolicy->subunitFor($counterCurrency);
		$subunitDifference = $baseCurrencySubunit - $counterCurrencySubunit;

		$ratio = (string) Number::fromString($ratio)->base10($subunitDifference);

		$counterValue = $money->multiply($ratio, $roundingMode);

		return new Money($counterValue->getAmount(), $counterCurrency);
	}

	private function quoteFromDomain(Currency $baseCurrency, Currency $counterCurrency): CurrencyPair
	{
		if ($baseCurrency->getCode() !== $counterCurrency->getCode()) {
			throw new DomainException('Cant quote different currencies');
		}
		$ratio = DomainCurrenciesPolicy::DOMAIN_SUBUNIT * 10;
		return new CurrencyPair($baseCurrency, $counterCurrency, $ratio);
	}

	private function quoteToDomain(Currency $baseCurrency, Currency $counterCurrency): CurrencyPair
	{
		if ($baseCurrency->getCode() !== $counterCurrency->getCode()) {
			throw new DomainException('Cant quote different currencies');
		}
		$ratio = DomainCurrenciesPolicy::DOMAIN_SUBUNIT / 10;
		return new CurrencyPair($baseCurrency, $counterCurrency, $ratio);
	}
}