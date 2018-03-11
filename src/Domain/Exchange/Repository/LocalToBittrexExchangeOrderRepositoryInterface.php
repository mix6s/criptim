<?php


namespace Domain\Exchange\Repository;


use Domain\Exception\EntityNotFoundException;
use Domain\Exchange\Entity\LocalToBittrexExchangeOrder;
use Domain\Exchange\ValueObject\LocalToBittrexExchangeOrderId;
use Domain\Exchange\ValueObject\OrderId;

interface LocalToBittrexExchangeOrderRepositoryInterface
{
	public function save(LocalToBittrexExchangeOrder $order): void;

	/**
	 * @param OrderId $id
	 * @return LocalToBittrexExchangeOrder
	 * @throws EntityNotFoundException
	 */
	public function findByOrderId(OrderId $id): LocalToBittrexExchangeOrder;

	/**
	 * @param LocalToBittrexExchangeOrderId $id
	 * @return LocalToBittrexExchangeOrder
	 * @throws EntityNotFoundException
	 */
	public function findByBittrexExchangeOrderId(LocalToBittrexExchangeOrderId $id): LocalToBittrexExchangeOrder;
}