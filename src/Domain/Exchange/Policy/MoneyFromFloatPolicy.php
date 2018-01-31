<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 16.01.2018
 * Time: 23:08
 */

namespace Domain\Exchange\Policy;


use Domain\Policy\DomainCurrenciesPolicy;
use Money\Currency;
use Money\Money;
use Money\Parser\DecimalMoneyParser;

class MoneyFromFloatPolicy implements MoneyFromFloatPolicyInterface
{
	/**
	 * @var DomainCurrenciesPolicy
	 */
	private $currencies;
	/**
	 * @var DecimalMoneyParser
	 */
	private $parser;

	public function __construct()
	{
		$this->currencies = new DomainCurrenciesPolicy();
		$this->parser = new DecimalMoneyParser($this->currencies);
	}

	public function getMoney(Currency $currency, float $amount): Money
	{
		$decimals = $this->currencies->subunitFor($currency);
		return $this->parser->parse(number_format($amount, $decimals, '.', ''), $currency);
	}
}