<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 05.01.2018
 * Time: 23:21
 */

namespace Domain\Repository;


use Domain\Entity\DepositInvoice;
use Domain\Exception\EntityNotFoundException;

interface DepositInvoiceRepositoryInterface
{
	/**
	 * @param DepositInvoice $depositInvoice
	 */
	public function save(DepositInvoice $depositInvoice);

	/**
	 * @param $depositInvoiceId
	 * @throws EntityNotFoundException
	 * @return DepositInvoice
	 */
	public function findById($depositInvoiceId): DepositInvoice;
}