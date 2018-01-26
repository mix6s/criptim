<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/26/18
 * Time: 7:38 PM
 */

namespace Domain\Exchange\UseCase\Response;


use Domain\Exchange\Entity\BotTradingSessionAccount;

class GetBotTradingSessionAccountResponse
{
	/**
	 * @var BotTradingSessionAccount
	 */
	private $botTradingSessionAccount;

	public function __construct(BotTradingSessionAccount $botTradingSessionAccount)
	{
		$this->botTradingSessionAccount = $botTradingSessionAccount;
	}

	/**
	 * @return BotTradingSessionAccount
	 */
	public function getBotTradingSessionAccount(): BotTradingSessionAccount
	{
		return $this->botTradingSessionAccount;
	}
}