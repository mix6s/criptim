<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 06.01.2018
 * Time: 0:40
 */

namespace Domain\Tests\UseCase\CreateInvestor;


use Domain\Entity\Investor;
use Domain\Tests\Factory\InvestorIdentityFactory;
use Domain\Tests\Repository\InvestorRepository;
use Domain\Tests\UseCaseTestCase;
use Domain\UseCase;

class HandlerTest extends UseCaseTestCase
{
	/** @var  UseCase\CreateInvestor\Handler */
	private $useCaseHandler;
	/** @var  InvestorRepository */
	private $investorRepository;
	/** @var  InvestorIdentityFactory */
	private $investorIdentityFactory;

	public function setUp()
	{
		parent::setUp();
		$this->investorRepository = new InvestorRepository();
		$this->investorIdentityFactory = new InvestorIdentityFactory();
		$this->useCaseHandler = new UseCase\CreateInvestor\Handler(
			$this->investorRepository,
			$this->investorIdentityFactory
		);
	}

	public function testHandle()
	{
		$response = $this->useCaseHandler->handle();
		$this->assertInstanceOf(UseCase\CreateInvestor\Response::class, $response);
		$investor = $response->getInvestor();
		$this->assertInstanceOf(Investor::class, $investor);
		$response = $this->useCaseHandler->handle();
		$this->assertNotEquals($investor->getId(), $response->getInvestor()->getId());
		$this->investorRepository->findById($response->getInvestor()->getId());
	}
}