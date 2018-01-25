<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/17/18
 * Time: 5:13 PM
 */

namespace DomainBundle\Exchange\Repository;


use Doctrine\ORM\EntityRepository;
use Domain\Exception\EntityNotFoundException;
use Domain\Exchange\Entity\Bot;
use Domain\Exchange\Repository\BotRepositoryInterface;
use Domain\Exchange\ValueObject\BotId;
use Domain\Exchange\ValueObject\ExchangeId;

class BotRepository extends EntityRepository implements BotRepositoryInterface
{

	/**
	 * @param ExchangeId $exchangeId
	 * @return Bot[]
	 */
	public function findByExchangeId(ExchangeId $exchangeId): array
	{
		return $this->findBy([
			'exchangeId' => $exchangeId
		]);
	}

	public function findById(BotId $botId): Bot
	{
		/** @var Bot $bot */
		$bot = $this->find($botId);
		if (empty($user)) {
			throw new EntityNotFoundException(sprintf('Bot with id %d not found', (string)$botId));
		}
		return $bot;
	}

	public function save(Bot $bot)
	{
		$this->getEntityManager()->persist($bot);
		$this->getEntityManager()->flush($bot);
	}
}