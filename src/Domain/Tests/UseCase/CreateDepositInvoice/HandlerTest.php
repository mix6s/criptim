<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 06.01.2018
 * Time: 0:07
 */

namespace Domain\Tests\UseCase\CreateDepositInvoice;


use Domain\Entity\DepositInvoice;
use Domain\Tests\Factory\DepositInvoiceIdentityFactory;
use Domain\Tests\Factory\InvestorIdentityFactory;
use Domain\Tests\Repository\DepositInvoiceRepository;
use Domain\Tests\Repository\InvestorRepository;
use Domain\Tests\UseCaseTestCase;
use Domain\UseCase as UseCase;
use Domain\ValueObject\DepositMoney;
use Domain\ValueObject\DepositPayMethod;
use Money\Money;

class HandlerTest extends UseCaseTestCase
{
	/** @var  UseCase\CreateDepositInvoice\Handler */
	private $useCaseHandler;
	/** @var  InvestorRepository */
	private $investorRepository;
	/** @var  DepositInvoiceRepository */
	private $depositInvoiceRepository;
	/** @var  DepositInvoiceIdentityFactory */
	private $depositInvoiceIdentityFactory;
	/** @var  InvestorIdentityFactory */
	private $investorIdentityFactory;
	/** @var  UseCase\CreateInvestor\Handler */
	private $createInvestorUseCaseHandler;

	public function setUp()
	{
		parent::setUp();
		$this->investorRepository = new InvestorRepository();
		$this->depositInvoiceRepository = new DepositInvoiceRepository();
		$this->depositInvoiceIdentityFactory = new DepositInvoiceIdentityFactory();
		$this->investorIdentityFactory = new InvestorIdentityFactory();
		$this->useCaseHandler = new UseCase\CreateDepositInvoice\Handler(
			$this->investorRepository,
			$this->depositInvoiceIdentityFactory,
			$this->depositInvoiceRepository
		);
		$this->createInvestorUseCaseHandler = new UseCase\CreateInvestor\Handler(
			$this->investorRepository,
			$this->investorIdentityFactory
		);
	}


	public function testHandle()
	{
		$investor = $this->createInvestorUseCaseHandler->handle()->getInvestor();
		$method = DepositPayMethod::cc();
		$sum = new DepositMoney(Money::RUB(500));

		$response = $this->useCaseHandler->handle(
			new UseCase\CreateDepositInvoice\Request(
				$investor->getId(),
				$sum,
				$method
			)
		);
		$this->assertInstanceOf(UseCase\CreateDepositInvoice\Response::class, $response);
		$invoice = $response->getDepositInvoice();
		$this->assertInstanceOf(DepositInvoice::class, $invoice);
		$this->assertEquals(DepositInvoice::STATUS_OPEN, $invoice->getStatus());
		$this->assertEquals(new DepositMoney(Money::RUB(500)), $invoice->getInvoiceSum());
		$this->assertEquals(DepositPayMethod::cc(), $invoice->getPayMethod());
		$this->assertEquals($investor->getId(), $invoice->getInvestorId());
	}
}