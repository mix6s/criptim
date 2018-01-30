<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/27/18
 * Time: 3:44 PM
 */

namespace Domain\Exchange\UseCase\Response;


use Domain\Exchange\Entity\BotTradingSessionAccount;
use Money\Money;

class GetBotTradingSessionBalancesResponse
{
	/**
	 * @var Money
	 */
	private $accountBalance;
	/**
	 * @var Money
	 */
	private $inOrdersBalance;
	/**
	 * @var Money
	 */
	private $availableBalance;
	/**
	 * @var BotTradingSessionAccount
	 */
	private $botTradingSessionAccount;
	/**
	 * @var Money
	 */
	private $startBalance;

	public function __construct(BotTradingSessionAccount $botTradingSessionAccount, Money $accountBalance, Money $inOrdersBalance, Money $availableBalance, Money $startBalance)
	{
		$this->accountBalance = $accountBalance;
		$this->inOrdersBalance = $inOrdersBalance;
		$this->availableBalance = $availableBalance;
		$this->botTradingSessionAccount = $botTradingSessionAccount;
		$this->startBalance = $startBalance;
	}

	/**
	 * @return Money
	 */
	public function getAccountBalance(): Money
	{
		return $this->accountBalance;
	}

	/**
	 * @return Money
	 */
	public function getInOrdersBalance(): Money
	{
		return $this->inOrdersBalance;
	}

	/**
	 * @return Money
	 */
	public function getAvailableBalance(): Money
	{
		return $this->availableBalance;
	}

	/**
	 * @return BotTradingSessionAccount
	 */
	public function getBotTradingSessionAccount(): BotTradingSessionAccount
	{
		return $this->botTradingSessionAccount;
	}

	/**
	 * @return Money
	 */
	public function getStartBalance(): Money
	{
		return $this->startBalance;
	}
}