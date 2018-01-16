<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 16.01.2018
 * Time: 23:08
 */

namespace Domain\Exchange\Policy;


use Domain\Policy\CryptoCurrenciesPolicy;
use Money\Currency;
use Money\Money;
use Money\Parser\DecimalMoneyParser;

class MoneyFromFloatPolicy implements MoneyFromFloatPolicyInterface
{
	/**
	 * @var CryptoCurrenciesPolicy
	 */
	private $currencies;
	/**
	 * @var DecimalMoneyParser
	 */
	private $parser;

	public function __construct()
	{
		$this->currencies = new CryptoCurrenciesPolicy();
		$this->parser = new DecimalMoneyParser($this->currencies);
	}

	public function getMoney(Currency $currency, float $amount): Money
	{
		return $this->parser->parse((string)$amount, $currency);
	}
}