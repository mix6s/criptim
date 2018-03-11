<?php


namespace DomainBundle\Exchange\Repository;


use Doctrine\ORM\EntityRepository;
use Domain\Exception\EntityNotFoundException;
use Domain\Exchange\Entity\LocalToBittrexExchangeOrder;
use Domain\Exchange\ValueObject\LocalToBittrexExchangeOrderId;
use Domain\Exchange\ValueObject\OrderId;
use Domain\Exchange\Repository\LocalToBittrexExchangeOrderRepositoryInterface;

class LocalToBittrexExchangeOrderRepository extends EntityRepository implements LocalToBittrexExchangeOrderRepositoryInterface
{

	public function save(LocalToBittrexExchangeOrder $order): void
	{
		$this->getEntityManager()->persist($order);
		$this->getEntityManager()->flush($order);
	}

	/**
	 * @param OrderId $id
	 * @return LocalToBittrexExchangeOrder
	 * @throws EntityNotFoundException
	 */
	public function findByOrderId(OrderId $id): LocalToBittrexExchangeOrder
	{
		// TODO: Implement findByOrderId() method.
	}

	/**
	 * @param LocalToBittrexExchangeOrderId $id
	 * @return LocalToBittrexExchangeOrder
	 * @throws EntityNotFoundException
	 */
	public function findByBittrexExchangeOrderId(LocalToBittrexExchangeOrderId $id): LocalToBittrexExchangeOrder
	{
		// TODO: Implement findByBittrexExchangeOrderId() method.
	}
}