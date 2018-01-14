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
	const TYPE_MAIN = 'main';
	const TYPE_TRADING = 'trading';
	/**
	 * @var BotId
	 */
	private $botId;
	/**
	 * @var string
	 */
	private $type;

	public function __construct(BotId $botId, ExchangeId $exchangeId, Currency $currency, string $type)
	{
		parent::__construct($exchangeId, $currency);
		$this->botId = $botId;
		$this->type = $type;
	}


	/**
	 * @return BotId
	 */
	public function getBotId(): BotId
	{
		return $this->botId;
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return $this->type;
	}
}