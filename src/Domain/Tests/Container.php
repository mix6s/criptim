<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 06.01.2018
 * Time: 21:25
 */

namespace Domain\Tests;


use Domain\Tests\Factory\DepositInvoiceIdentityFactory;
use Domain\Tests\Factory\InvestorAccountIdentityFactory;
use Domain\Tests\Factory\InvestorAccountTransactionIdentityFactory;
use Domain\Tests\Factory\InvestorIdentityFactory;
use Domain\Tests\Policy\SimpleDepositToBitMoneyPolicy;
use Domain\Tests\Repository\DepositInvoiceRepository;
use Domain\Tests\Repository\InvestorAccountRepository;
use Domain\Tests\Repository\InvestorAccountTransactionRepository;
use Domain\Tests\Repository\InvestorRepository;
use Domain\UseCase;

class Container
{
	private $investorRepository;
	private $investorIdentityFactory;
	private $depositInvoiceRepository;
	private $depositInvoiceIdentityFactory;
	private $investorAccountRepository;
	private $investorAccountTransactionRepository;
	private $investorAccountIdentityFactory;
	private $investorAccountIdentityTransactionFactory;

	public function __construct()
	{
		$this->investorRepository = new InvestorRepository();
		$this->investorIdentityFactory = new InvestorIdentityFactory();
		$this->investorAccountIdentityFactory = new InvestorAccountIdentityFactory();
		$this->investorAccountIdentityTransactionFactory = new InvestorAccountTransactionIdentityFactory();
		$this->depositInvoiceRepository = new DepositInvoiceRepository();
		$this->depositInvoiceIdentityFactory = new DepositInvoiceIdentityFactory();
		$this->investorAccountRepository = new InvestorAccountRepository();
		$this->investorAccountTransactionRepository = new InvestorAccountTransactionRepository();
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
		return new UseCase\PayDepositInvoice\Handler(
			$this->depositInvoiceRepository,
			new SimpleDepositToBitMoneyPolicy(),
			$this->getChangeInvestorAccountBalanceUseCaseHandler(),
			$this->getCreateInvestorAccountTransactionUseCaseHandler()
		);
	}

	public function getChangeInvestorAccountBalanceUseCaseHandler(): UseCase\ChangeInvestorAccountBalance\Handler
	{
		return new UseCase\ChangeInvestorAccountBalance\Handler(
			$this->getInvestorAccountTransactionRepository(),
			$this->getInvestorAccountRepository()
		);
	}

	public function getCreateInvestorAccountTransactionUseCaseHandler(): UseCase\CreateInvestorAccountTransaction\Handler
	{
		return new UseCase\CreateInvestorAccountTransaction\Handler(
			$this->getInvestorAccountTransactionIdentityFactory(),
			$this->getFindInvestorAccountUseCaseHandler(),
			$this->getInvestorAccountTransactionRepository()
		);
	}

	public function getFindInvestorAccountUseCaseHandler(): UseCase\FindInvestorAccount\Handler
	{
		return new UseCase\FindInvestorAccount\Handler(
			$this->getInvestorAccountRepository(),
			$this->getInvestorAccountIdentityFactory()
		);
	}

	public function getTransferToTradingAccountUseCase(): UseCase\TransferInvestorBitMoneyFromMainToTradingAccount\Handler
	{
		return new UseCase\TransferInvestorBitMoneyFromMainToTradingAccount\Handler(

		);
	}

	/**
	 * @return InvestorAccountRepository
	 */
	public function getInvestorAccountRepository(): InvestorAccountRepository
	{
		return $this->investorAccountRepository;
	}

	/**
	 * @return InvestorAccountTransactionRepository
	 */
	public function getInvestorAccountTransactionRepository(): InvestorAccountTransactionRepository
	{
		return $this->investorAccountTransactionRepository;
	}

	/**
	 * @return InvestorAccountIdentityFactory
	 */
	public function getInvestorAccountIdentityFactory(): InvestorAccountIdentityFactory
	{
		return $this->investorAccountIdentityFactory;
	}

	/**
	 * @return InvestorAccountTransactionIdentityFactory
	 */
	public function getInvestorAccountTransactionIdentityFactory(): InvestorAccountTransactionIdentityFactory
	{
		return $this->investorAccountIdentityTransactionFactory;
	}
}