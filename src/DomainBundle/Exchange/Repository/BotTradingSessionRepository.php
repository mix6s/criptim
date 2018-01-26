<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/17/18
 * Time: 5:40 PM
 */

namespace DomainBundle\Exchange\Repository;


use Doctrine\ORM\EntityRepository;
use Domain\Exception\EntityNotFoundException;
use Domain\Exchange\Entity\BotTradingSession;
use Domain\Exchange\Repository\BotTradingSessionRepositoryInterface;
use Domain\Exchange\ValueObject\BotId;

class BotTradingSessionRepository extends EntityRepository implements BotTradingSessionRepositoryInterface
{

	/**
	 * @param BotId $botId
	 * @return BotTradingSession
	 * @throws EntityNotFoundException
	 */
	public function findLastByBotId(BotId $botId): BotTradingSession
	{
		$session = $this->getEntityManager()->createQueryBuilder()
			->select('s')
			->from('Domain\Exchange\Entity\BotTradingSession', 's')
			->where('s.botId = :id')
			->setParameter('id', $botId)
			->orderBy('s.updatedAt', 'DESC')
			->setMaxResults(1)
			->getQuery()
			->getOneOrNullResult();
		if ($session === null) {
			throw new EntityNotFoundException('Latest BotTradingSession not found');
		}
		return $session;
	}

	/**
	 * @param BotTradingSession $session
	 */
	public function save(BotTradingSession $session)
	{
		$this->getEntityManager()->persist($session);
		$this->getEntityManager()->flush($session);
	}
}