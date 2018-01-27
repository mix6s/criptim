<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 14.01.2018
 * Time: 15:28
 */

namespace Domain\Exchange\Repository;


use Domain\Exception\EntityNotFoundException;
use Domain\Exchange\Entity\BotTradingSession;
use Domain\Exchange\ValueObject\BotId;
use Domain\Exchange\ValueObject\BotTradingSessionId;

interface BotTradingSessionRepositoryInterface
{
	/**
	 * @param BotId $botId
	 * @return BotTradingSession
	 * @throws EntityNotFoundException
	 */
	public function findLastByBotId(BotId $botId): BotTradingSession;

	/**
	 * @param BotTradingSessionId $botTradingSessionId
	 * @return BotTradingSession
	 * @throws EntityNotFoundException
	 */
	public function findById(BotTradingSessionId $botTradingSessionId): BotTradingSession;

	/**
	 * @param BotTradingSession $session
	 */
	public function save(BotTradingSession $session);
}