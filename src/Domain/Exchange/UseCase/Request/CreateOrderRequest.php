<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/26/18
 * Time: 8:22 PM
 */

namespace Domain\Exchange\UseCase\Request;


use Domain\Exchange\ValueObject\BotTradingSessionId;
use Domain\Exchange\ValueObject\ExchangeId;
use Money\CurrencyPair;

class CreateOrderRequest
{
	/** @var  ExchangeId */
	private $exchangeId;
	/** @var  BotTradingSessionId */
	private $botTradingSessionId;
	/** @var  string */
	private $type;

	/** @var  CurrencyPair */
	private $symbol;

	/** @var float */
	private $price;
	/** @var  float */
	private $amount;

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

	/**
	 * @return BotTradingSessionId
	 */
	public function getBotTradingSessionId()
	{
		return $this->botTradingSessionId;
	}

	/**
	 * @param BotTradingSessionId $botTradingSessionId
	 */
	public function setBotTradingSessionId(BotTradingSessionId $botTradingSessionId)
	{
		$this->botTradingSessionId = $botTradingSessionId;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param string $type
	 */
	public function setType(string $type)
	{
		$this->type = $type;
	}

	/**
	 * @return CurrencyPair
	 */
	public function getSymbol()
	{
		return $this->symbol;
	}

	/**
	 * @param CurrencyPair $symbol
	 */
	public function setSymbol(CurrencyPair $symbol)
	{
		$this->symbol = $symbol;
	}

	/**
	 * @return float
	 */
	public function getPrice()
	{
		return $this->price;
	}

	/**
	 * @param float $price
	 */
	public function setPrice(float $price)
	{
		$this->price = $price;
	}

	/**
	 * @return float
	 */
	public function getAmount()
	{
		return $this->amount;
	}

	/**
	 * @param float $amount
	 */
	public function setAmount(float $amount)
	{
		$this->amount = $amount;
	}



}