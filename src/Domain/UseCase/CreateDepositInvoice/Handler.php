<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 05.01.2018
 * Time: 22:35
 */

namespace Domain\UseCase\CreateDepositInvoice;


use Domain\Entity\DepositInvoice;
use Domain\Factory\DepositInvoiceIdentityFactoryInterface;
use Domain\Repository\DepositInvoiceRepositoryInterface;
use Domain\Repository\InvestorRepositoryInterface;

class Handler
{
	/**
	 * @var InvestorRepositoryInterface
	 */
	private $investorRepository;
	/**
	 * @var DepositInvoiceIdentityFactoryInterface
	 */
	private $depositInvoiceIdentityFactory;
	/**
	 * @var DepositInvoiceRepositoryInterface
	 */
	private $depositInvoiceRepository;

	public function __construct(
		InvestorRepositoryInterface $investorRepository,
		DepositInvoiceIdentityFactoryInterface $depositInvoiceIdentityFactory,
		DepositInvoiceRepositoryInterface $depositInvoiceRepository
	) {
		$this->investorRepository = $investorRepository;
		$this->depositInvoiceIdentityFactory = $depositInvoiceIdentityFactory;
		$this->depositInvoiceRepository = $depositInvoiceRepository;
	}

	public function handle(Request $request): Response
	{
		$investor = $this->investorRepository->findById($request->getInvestorId());
		$invoiceId = $this->depositInvoiceIdentityFactory->getNextId();
		$invoice = new DepositInvoice($invoiceId, $investor->getId(), $request->getInvoiceSum(), $request->getPayMethod());
		$this->depositInvoiceRepository->save($invoice);
		return new Response($invoice);
	}
}