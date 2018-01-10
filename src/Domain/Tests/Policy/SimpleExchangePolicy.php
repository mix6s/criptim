<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 10.01.18
 * Time: 21:14
 */

namespace Domain\Tests\Policy;


use Money\Currency;
use Money\CurrencyPair;
use Money\Exception\UnresolvableCurrencyPairException;
use Money\Exchange;

class SimpleExchangePolicy implements Exchange
{

    /**
     * Returns a currency pair for the passed currencies with the rate coming from a third-party source.
     *
     * @param Currency $baseCurrency
     * @param Currency $counterCurrency
     *
     * @return CurrencyPair
     *
     * @throws UnresolvableCurrencyPairException When there is no currency pair (rate) available for the given currencies
     */
    public function quote(Currency $baseCurrency, Currency $counterCurrency)
    {
        $ratio = 1/1000000;
        return new CurrencyPair($baseCurrency, $counterCurrency, $ratio);
    }
}