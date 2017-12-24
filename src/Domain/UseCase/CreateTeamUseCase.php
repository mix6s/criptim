<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 25.07.2017
 * Time: 19:26
 */

namespace Domain\UseCase;


use Domain\DTO\Request\CreateTeamRequest;
use Domain\DTO\Response\CreateTeamResponse;
use Domain\Entity\Team;

/**
 * Class CreateTeamUseCase
 * @package Domain\UseCase
 */
class CreateTeamUseCase
{
	use UseCaseTrait;

	/**
	 * @param CreateTeamRequest $request
	 * @return CreateTeamResponse
	 */
	public function execute(CreateTeamRequest $request): CreateTeamResponse
	{
		$teamRepository = $this->getContainer()->getTeamRepository();
		$team = Team::create($teamRepository->getNextId(), $request->getMetadata());
		$teamRepository->save($team);
		return new CreateTeamResponse($team);
	}
}