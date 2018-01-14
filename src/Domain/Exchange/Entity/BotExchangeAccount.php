<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 14.01.2018
 * Time: 13:02
 */

namespace Domain\Exchange\Entity;


use Domain\Exchange\ValueObject\BotId;
use Domain\Exchange\ValueObject\ExchangeId;
use Money\Currency;

class BotExchangeAccount extends ExchangeAccount
{
	/**
	 * @var BotId
	 */
	protected $botId;

	public function __construct(BotId $botId, ExchangeId $exchangeId, Currency $currency)
	{
		parent::__construct($exchangeId, $currency);
		$this->botId = $botId;
	}


	/**
	 * @return BotId
	 */
	public function getBotId(): BotId
	{
		return $this->botId;
	}
}