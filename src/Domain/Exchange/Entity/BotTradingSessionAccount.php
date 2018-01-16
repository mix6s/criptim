<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 14.01.2018
 * Time: 13:02
 */

namespace Domain\Exchange\Entity;


use Domain\Entity\Account;
use Domain\Exchange\ValueObject\BotTradingSessionId;
use Money\Currency;

class BotTradingSessionAccount extends Account
{
	/**
	 * @var BotTradingSessionId
	 */
	protected $botTradingSessionId;

	public function __construct(BotTradingSessionId $botTradingSessionId, Currency $currency)
	{
		parent::__construct($currency);
		$this->botTradingSessionId = $botTradingSessionId;
	}

	/**
	 * @return BotTradingSessionId
	 */
	public function getBotTradingSessionId(): BotTradingSessionId
	{
		return $this->botTradingSessionId;
	}
}