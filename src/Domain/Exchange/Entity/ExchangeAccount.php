<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 14.01.2018
 * Time: 13:16
 */

namespace Domain\Exchange\Entity;


use Domain\Entity\Account;
use Domain\Exchange\ValueObject\ExchangeId;
use Money\Currency;

class ExchangeAccount extends Account
{
	/**
	 * @var ExchangeId
	 */
	private $exchangeId;

	public function __construct(ExchangeId $exchangeId, Currency $currency)
	{
		parent::__construct($currency);
		$this->exchangeId = $exchangeId;
	}

	/**
	 * @return ExchangeId
	 */
	public function getExchangeId(): ExchangeId
	{
		return $this->exchangeId;
	}
}