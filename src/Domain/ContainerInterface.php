<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 25.07.2017
 * Time: 18:33
 */

namespace Domain;


use Domain\Repository\GameEventRepositoryInterface;
use Domain\Repository\GameRepositoryInterface;
use Domain\Repository\LeagueRepositoryInterface;
use Domain\Repository\PlayerRepositoryInterface;
use Domain\Repository\SeasonRepositoryInterface;
use Domain\Repository\SeasonTeamMemberRepositoryInterface;
use Domain\Repository\SeasonTeamRepositoryInterface;
use Domain\Repository\TeamRepositoryInterface;

/**
 * Interface ContainerInterface
 * @package Domain
 */
interface ContainerInterface
{
	/**
	 * @return SeasonRepositoryInterface
	 */
	public function getSeasonRepository(): SeasonRepositoryInterface;
}