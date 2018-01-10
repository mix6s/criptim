<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 06.01.2018
 * Time: 21:22
 */

namespace Domain\UseCase\PayDepositInvoice;


use Domain\Entity\DepositInvoice;
use Domain\Entity\InvestorAccountTransaction;
use Domain\Exception\DepositInvoiceNotAvailableForPay;
use Domain\Policy\DepositMoneyToBitMoneyConvertPolicy;
use Domain\Repository\DepositInvoiceRepositoryInterface;
use Domain\UseCase;

class Handler
{
	/**
	 * @var DepositInvoiceRepositoryInterface
	 */
	private $depositInvoiceRepository;
	/**
	 * @var DepositMoneyToBitMoneyConvertPolicy
	 */
	private $depositToBitMoneyConvertPolicy;
	/**
	 * @var UseCase\ChangeInvestorAccountBalance\Handler
	 */
	private $changeInvestorAccountBalanceHandler;

	/**
	 * @var UseCase\CreateInvestorAccountTransaction\Handler
	 */
	private $createInvestorAccountTransactionHandler;

	public function __construct(
		DepositInvoiceRepositoryInterface $depositInvoiceRepository,
		DepositMoneyToBitMoneyConvertPolicy $moneyConvertPolicy,
		UseCase\ChangeInvestorAccountBalance\Handler $changeInvestorAccountBalanceHandler,
		UseCase\CreateInvestorAccountTransaction\Handler $createInvestorAccountTransactionHandler
	)
	{
		$this->depositInvoiceRepository = $depositInvoiceRepository;
		$this->depositToBitMoneyConvertPolicy = $moneyConvertPolicy;
		$this->changeInvestorAccountBalanceHandler = $changeInvestorAccountBalanceHandler;
		$this->createInvestorAccountTransactionHandler = $createInvestorAccountTransactionHandler;
	}

    /**
     * @param Request $request
     * @return Response
     * @throws DepositInvoiceNotAvailableForPay
     * @throws \Domain\Exception\DomainException
     * @throws \Domain\Exception\EntityNotFoundException
     */
	public function handle(Request $request): Response
	{
		$billingInvoice = $request->getBillingInvoice();
		$depositInvoice = $this->depositInvoiceRepository->findById($billingInvoice->getDepositInvoiceIdentity());
		if ($depositInvoice->getStatus() !== DepositInvoice::STATUS_OPEN) {
			throw new DepositInvoiceNotAvailableForPay();
		}
		$bitMoneyToAdd = $this->depositToBitMoneyConvertPolicy->convert($billingInvoice->getDepositMoney());
		$depositInvoice->markAsPayed($billingInvoice, $bitMoneyToAdd);
		$this->depositInvoiceRepository->save($depositInvoice);


		$transaction = $this->createInvestorAccountTransactionHandler->handle(new UseCase\CreateInvestorAccountTransaction\Request(
			$depositInvoice->getInvestorId(),
			InvestorAccountTransaction::TYPE_DEPOSIT,
			$bitMoneyToAdd
		))->getTransaction();


		$this->changeInvestorAccountBalanceHandler->handle(new UseCase\ChangeInvestorAccountBalance\Request(
			$transaction->getId()
		));
		return new Response($depositInvoice);
	}
}