<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 25.07.2017
 * Time: 19:29
 */

namespace Domain\Repository;


use Domain\Entity\Game;
use Domain\Entity\SeasonTeam;
use Domain\Exception\EntityNotFoundException;

/**
 * Interface GameRepositoryInterface
 * @package Domain\Repository
 */
interface GameRepositoryInterface
{
	/**
	 * @return int
	 */
	public function getNextId(): int;

	/**
	 * @param int $id
	 * @return Game
	 * @throws EntityNotFoundException
	 */
	public function findById(int $id): Game;

	/**
	 * @return Game[]
	 */
	public function findBySeasonTeam(SeasonTeam $seasonTeam);
	/**
	 * @param Game $team
	 */
	public function save(Game $team);

	/**
	 * @param Game $team
	 */
	public function remove(Game $team);
}