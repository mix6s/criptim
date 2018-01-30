<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/27/18
 * Time: 4:24 PM
 */

namespace DomainBundle\Exchange\Repository;


use Doctrine\ORM\EntityRepository;
use Domain\Exception\EntityNotFoundException;
use Domain\Exchange\Entity\Order;
use Domain\Exchange\Repository\OrderRepositoryInterface;
use Domain\Exchange\ValueObject\BotTradingSessionId;
use Domain\Exchange\ValueObject\ExchangeId;
use Domain\Exchange\ValueObject\OrderId;

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
			->setParameter('statuses', [Order::STATUS_NEW, Order::STATUS_PARTIALLY_FILLED])
			->setParameter('exchange_id', $exchangeId)
			->getResult();
	}

	/**
	 * @param OrderId $orderId
	 * @return Order
	 */
	public function findById(OrderId $orderId): Order
	{
		/** @var Order $order */
		$order = $this->find($orderId);
		if (empty($order)) {
			throw new EntityNotFoundException(sprintf('Order with id %d not found', (string)$orderId));
		}
		return $order;
	}
}