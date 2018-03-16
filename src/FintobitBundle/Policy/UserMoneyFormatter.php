<?php


namespace FintobitBundle\Policy;


use Domain\Policy\DomainCurrenciesPolicy;
use DomainBundle\Exchange\Policy\CryptoMoneyFormatter;
use Money\Money;
use Money\MoneyFormatter;

class UserMoneyFormatter implements MoneyFormatter
{

	private $currencies;
	private $cryptoMoneyFormatter;

	public function __construct()
	{
		$this->currencies = new DomainCurrenciesPolicy();
		$this->cryptoMoneyFormatter = new CryptoMoneyFormatter();
	}

	public function format(Money $money): string
	{
		$defaultFormat = $this->cryptoMoneyFormatter->format($money);
		return substr($defaultFormat, 0, -DomainCurrenciesPolicy::DOMAIN_SUBUNIT);
	}

	public function formatWithCurrency(Money $money): string
	{
		$format = '%s %s';
		return vsprintf($format, [
			$this->format($money),
			$this->currencies->getFormattedCurrency($money->getCurrency())
		]);
	}

	public function currency(Money $money): string
	{
		return $this->currencies->getFormattedCurrency($money->getCurrency());
	}

}