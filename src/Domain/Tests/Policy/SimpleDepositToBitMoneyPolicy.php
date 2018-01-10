<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 07.01.2018
 * Time: 1:42
 */

namespace Domain\Tests\Policy;


use Domain\Policy\CryptoCurrenciesPolicy;
use Domain\Policy\DepositMoneyToBitMoneyConvertPolicy;
use Domain\Policy\DomainCurrenciesPolicy;
use Domain\ValueObject\BitMoney;
use Domain\ValueObject\DepositMoney;
use Money\Converter;
use Money\Currency;
use Money\Money;

class SimpleDepositToBitMoneyPolicy implements DepositMoneyToBitMoneyConvertPolicy
{
    /**
     * @var Converter
     */
    private $converter;

    public function __construct()
    {
        $this->converter = new Converter(new DomainCurrenciesPolicy(), new SimpleExchangePolicy());
    }

    public function convert(DepositMoney $depositMoney): BitMoney
	{
		return new BitMoney($this->converter->convert($depositMoney->getMoney(), new Currency(CryptoCurrenciesPolicy::BTC)));
	}
}