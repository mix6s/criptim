<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 06.01.2018
 * Time: 0:39
 */

namespace Domain\UseCase\CreateInvestor;


use Domain\Entity\Investor;
use Domain\Factory\InvestorIdentityFactoryInterface;
use Domain\Repository\InvestorRepositoryInterface;

class Handler
{
	/**
	 * @var InvestorRepositoryInterface
	 */
	private $investorRepository;
	/**
	 * @var InvestorIdentityFactoryInterface
	 */
	private $investorIdentityFactory;

	public function __construct(
		InvestorRepositoryInterface $investorRepository,
		InvestorIdentityFactoryInterface $investorIdentityFactory
	) {
		$this->investorRepository = $investorRepository;
		$this->investorIdentityFactory = $investorIdentityFactory;
	}

	public function handle(): Response
	{
		$investorId = $this->investorIdentityFactory->getNextId();
		$investor = new Investor($investorId);
		$this->investorRepository->save($investor);
		return new Response($investor);
	}
}