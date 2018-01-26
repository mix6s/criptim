<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/26/18
 * Time: 7:38 PM
 */

namespace Domain\Exchange\UseCase\Request;


use Domain\Exchange\ValueObject\BotTradingSessionId;
use Money\Currency;

class GetBotTradingSessionAccountRequest
{
	/** @var  BotTradingSessionId|null */
	private $botTradingSessionId;
	/** @var  Currency|null */
	private $currency;

	/**
	 * @return BotTradingSessionId|null
	 */
	public function getBotTradingSessionId()
	{
		return $this->botTradingSessionId;
	}

	/**
	 * @return Currency|null
	 */
	public function getCurrency()
	{
		return $this->currency;
	}

	/**
	 * @param BotTradingSessionId|null $botTradingSessionId
	 */
	public function setBotTradingSessionId(BotTradingSessionId $botTradingSessionId)
	{
		$this->botTradingSessionId = $botTradingSessionId;
	}

	/**
	 * @param Currency|null $currency
	 */
	public function setCurrency(Currency $currency)
	{
		$this->currency = $currency;
	}
}