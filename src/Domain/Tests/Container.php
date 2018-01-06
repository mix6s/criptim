<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 06.01.2018
 * Time: 21:25
 */

namespace Domain\Tests;


use Domain\Tests\Factory\DepositInvoiceIdentityFactory;
use Domain\Tests\Factory\InvestorIdentityFactory;
use Domain\Tests\Policy\SimpleDepositToBitMoneyPolicy;
use Domain\Tests\Repository\DepositInvoiceRepository;
use Domain\Tests\Repository\InvestorRepository;
use Domain\UseCase;

class Container
{
	private $investorRepository;
	private $investorIdentityFactory;
	private $depositInvoiceRepository;
	private $depositInvoiceIdentityFactory;

	public function __construct()
	{
		$this->investorRepository = new InvestorRepository();
		$this->investorIdentityFactory = new InvestorIdentityFactory();
		$this->depositInvoiceRepository = new DepositInvoiceRepository();
		$this->depositInvoiceIdentityFactory = new DepositInvoiceIdentityFactory();
	}

	/**
	 * @return InvestorRepository
	 */
	public function getInvestorRepository(): InvestorRepository
	{
		return $this->investorRepository;
	}

	/**
	 * @return InvestorIdentityFactory
	 */
	public function getInvestorIdentityFactory(): InvestorIdentityFactory
	{
		return $this->investorIdentityFactory;
	}

	/**
	 * @return DepositInvoiceRepository
	 */
	public function getDepositInvoiceRepository(): DepositInvoiceRepository
	{
		return $this->depositInvoiceRepository;
	}

	/**
	 * @return DepositInvoiceIdentityFactory
	 */
	public function getDepositInvoiceIdentityFactory(): DepositInvoiceIdentityFactory
	{
		return $this->depositInvoiceIdentityFactory;
	}

	public function getCreateInvestorUseCaseHandler(): UseCase\CreateInvestor\Handler
	{
		return new UseCase\CreateInvestor\Handler(
			$this->investorRepository,
			$this->investorIdentityFactory
		);
	}

	public function getCreateDepositInvoiceUseCaseHandler(): UseCase\CreateDepositInvoice\Handler
	{
		return new UseCase\CreateDepositInvoice\Handler(
			$this->investorRepository,
			$this->depositInvoiceIdentityFactory,
			$this->depositInvoiceRepository
		);
	}

	public function getPayDepositInvoiceUseCaseHandler(): UseCase\PayDepositInvoice\Handler
	{
		return new UseCase\PayDepositInvoice\Handler($this->depositInvoiceRepository, new SimpleDepositToBitMoneyPolicy());
	}
}