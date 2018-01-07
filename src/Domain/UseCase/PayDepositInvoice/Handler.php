<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 06.01.2018
 * Time: 21:22
 */

namespace Domain\UseCase\PayDepositInvoice;


use Domain\Entity\DepositInvoice;
use Domain\Exception\DepositInvoiceNotAvailableForPay;
use Domain\Policy\DepositMoneyToBitMoneyConvertPolicy;
use Domain\Repository\DepositInvoiceRepositoryInterface;
use Money\Money;

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

	public function __construct(DepositInvoiceRepositoryInterface $depositInvoiceRepository, DepositMoneyToBitMoneyConvertPolicy $moneyConvertPolicy)
	{
		$this->depositInvoiceRepository = $depositInvoiceRepository;
		$this->depositToBitMoneyConvertPolicy = $moneyConvertPolicy;
	}

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
		return new Response($depositInvoice);
	}
}