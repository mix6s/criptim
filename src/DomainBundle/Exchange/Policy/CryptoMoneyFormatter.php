<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/26/18
 * Time: 2:32 PM
 */

namespace DomainBundle\Exchange\Policy;


use Domain\Policy\DomainCurrenciesPolicy;
use Money\Money;
use Money\MoneyFormatter;

class CryptoMoneyFormatter implements MoneyFormatter
{
	/**
	 * @var DomainCurrenciesPolicy
	 */
	private $currencies;

	public function __construct()
	{
		$this->currencies = new DomainCurrenciesPolicy();
	}

	/**
	 * {@inheritdoc}
	 */
	public function format(Money $money)
	{
		$valueBase = $money->getAmount();
		$negative = false;

		if ($valueBase[0] === '-') {
			$negative = true;
			$valueBase = substr($valueBase, 1);
		}

		$subunit = $this->currencies->subunitFor($money->getCurrency());
		$valueLength = strlen($valueBase);

		if ($valueLength > $subunit) {
			$formatted = substr($valueBase, 0, $valueLength - $subunit);
			$decimalDigits = substr($valueBase, $valueLength - $subunit);

			if (strlen($decimalDigits) > 0) {
				$formatted .= '.'.$decimalDigits;
			}
		} else {
			$formatted = '0.'.str_pad('', $subunit - $valueLength, '0').$valueBase;
		}

		if ($negative === true) {
			$formatted = '-'.$formatted;
		}

		return $formatted . ' ' . $this->currencies->getFormattedCurrency($money->getCurrency());
	}
}