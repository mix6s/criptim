<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 06.01.2018
 * Time: 0:04
 */

namespace Domain\Tests\Repository;


use Domain\Entity\DepositInvoice;
use Domain\Exception\EntityNotFoundException;
use Domain\Repository\DepositInvoiceRepositoryInterface;

class DepositInvoiceRepository implements DepositInvoiceRepositoryInterface
{
	use InMemoryRepositoryTrait;
	/**
	 * @param DepositInvoice $depositInvoice
	 */
	public function save(DepositInvoice $depositInvoice)
	{
		$this->storeEntity($depositInvoice, $depositInvoice->getId());
	}

	/**
	 * @param $depositInvoiceId
	 * @throws EntityNotFoundException
	 * @return DepositInvoice
	 */
	public function findById($depositInvoiceId): DepositInvoice
	{
		return $this->getEntity($depositInvoiceId);
	}
}