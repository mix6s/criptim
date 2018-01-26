<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/26/18
 * Time: 1:39 PM
 */

namespace Domain\Exchange\UseCase\Request;


use Domain\Exchange\ValueObject\BotId;
use Domain\Exchange\ValueObject\ExchangeId;
use Money\Currency;

class GetBotExchangeAccountRequest
{
	/** @var  Currency */
	private $currency;
	/** @var  BotId */
	private $botId;
	/** @var  ExchangeId */
	private $exchangeId;

	/**
	 * @return Currency
	 */
	public function getCurrency()
	{
		return $this->currency;
	}

	/**
	 * @param Currency $currency
	 */
	public function setCurrency(Currency $currency)
	{
		$this->currency = $currency;
	}

	/**
	 * @return BotId
	 */
	public function getBotId()
	{
		return $this->botId;
	}

	/**
	 * @param BotId $botId
	 */
	public function setBotId(BotId $botId)
	{
		$this->botId = $botId;
	}

	/**
	 * @return ExchangeId
	 */
	public function getExchangeId()
	{
		return $this->exchangeId;
	}

	/**
	 * @param ExchangeId $exchangeId
	 */
	public function setExchangeId(ExchangeId $exchangeId)
	{
		$this->exchangeId = $exchangeId;
	}

}