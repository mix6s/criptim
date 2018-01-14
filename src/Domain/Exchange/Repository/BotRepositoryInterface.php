<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 14.01.2018
 * Time: 13:56
 */

namespace Domain\Exchange\Repository;


use Domain\Exchange\Entity\Bot;
use Domain\Exchange\ValueObject\BotId;
use Domain\Exchange\ValueObject\ExchangeId;

interface BotRepositoryInterface
{
	/**
	 * @param ExchangeId $exchangeId
	 * @return Bot[]
	 */
	public function findByExchangeId(ExchangeId $exchangeId): array;

	public function findById(BotId $botId): Bot;
}