<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/27/18
 * Time: 4:24 PM
 */

namespace DomainBundle\Exchange\Repository;


use Doctrine\ORM\EntityRepository;
use Domain\Exchange\Entity\Order;
use Domain\Exchange\Repository\OrderRepositoryInterface;
use Domain\Exchange\ValueObject\BotTradingSessionId;
use Domain\Exchange\ValueObject\ExchangeId;

class OrderRepository extends EntityRepository implements OrderRepositoryInterface
{

	public function save(Order $order)
	{
		$this->getEntityManager()->persist($order);
		$this->getEntityManager()->flush($order);
	}

	/**
	 * @param BotTradingSessionId $sessionId
	 * @return Order[]
	 */
	public function findActive(BotTradingSessionId $sessionId): array
	{
		return $this->getEntityManager()->createQueryBuilder()
			->select('o')
			->from('Domain\Exchange\Entity\Order', 'o')
			->where('o.botTradingSessionId = :id')
			->andWhere('o.status in (:statuses)')
			->setParameter('id', $sessionId)
			->setParameter('statuses', [Order::STATUS_NEW, Order::STATUS_PARTIALLY_FILLED])
			->orderBy('o.id', 'DESC')
			->getQuery()
			->getResult();
	}

	public function findActiveByExchangeId(ExchangeId $exchangeId): array
	{
		return $this->createNativeNamedQuery('findActiveByExchangeId')
			->setParameter('currency', [Order::STATUS_NEW, Order::STATUS_PARTIALLY_FILLED])
			->setParameter('exchange_id', $exchangeId)
			->getResult();
	}
}