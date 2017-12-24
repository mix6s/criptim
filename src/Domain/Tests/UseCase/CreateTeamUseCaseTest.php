<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 25.07.2017
 * Time: 22:40
 */

namespace Domain\Tests\UseCase;

use Domain\DTO\Request\CreateTeamRequest;
use Domain\Tests\UseCaseTestCase;
use Domain\UseCase\CreateTeamUseCase;

/**
 * Class CreateTeamUseCaseTest
 * @package Domain\Tests\UseCase
 */
class CreateTeamUseCaseTest extends UseCaseTestCase
{
	/** @var  CreateTeamUseCase */
	private $useCase;

	public function setUp()
	{
		parent::setUp();
		$this->useCase = new CreateTeamUseCase($this->getContainer());
	}

	public function testShouldCreateTeam()
	{
		$response = $this->useCase->execute(new CreateTeamRequest([]));
		$storedTeam = $this->getContainer()->getTeamRepository()->findById($response->getTeam()->getId());
		$this->assertEquals($storedTeam, $response->getTeam());
	}
}
