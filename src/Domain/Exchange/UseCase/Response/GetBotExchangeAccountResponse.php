<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/26/18
 * Time: 1:42 PM
 */

namespace Domain\Exchange\UseCase\Response;


use Domain\Exchange\Entity\BotExchangeAccount;

class GetBotExchangeAccountResponse
{
	/**
	 * @var BotExchangeAccount
	 */
	private $botExchangeAccount;

	public function __construct(BotExchangeAccount $botExchangeAccount)
	{
		$this->botExchangeAccount = $botExchangeAccount;
	}

	/**
	 * @return BotExchangeAccount
	 */
	public function getBotExchangeAccount(): BotExchangeAccount
	{
		return $this->botExchangeAccount;
	}
}