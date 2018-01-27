<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/27/18
 * Time: 3:44 PM
 */

namespace Domain\Exchange\UseCase\Request;


use Domain\Exchange\ValueObject\BotTradingSessionId;
use Money\Currency;

class GetBotTradingSessionBalancesRequest
{
	/**
	 * @var BotTradingSessionId|null
	 */
	private $botTradingSessionId;
	/**
	 * @var Currency|null
	 */
	private $currency;

	/**
	 * @return BotTradingSessionId|null
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
	 * @return Currency|null
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


}