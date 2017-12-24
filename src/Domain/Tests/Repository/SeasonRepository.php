<?php
/**
 * Created by PhpStorm.
 * User: Mix6s
 * Date: 26.07.2017
 * Time: 8:23
 */

namespace Domain\Tests\Repository;


use Domain\Entity\Season;
use Domain\Exception\EntityNotFoundException;
use Domain\Repository\SeasonRepositoryInterface;
use Domain\Repository\TeamRepositoryInterface;

/**
 * Class SeasonRepository
 * @package Domain\Tests\Repository
 */
class SeasonRepository implements SeasonRepositoryInterface
{
	use InMemoryRepositoryTrait;

	/**
	 * @param int $id
	 * @return Season
	 * @throws EntityNotFoundException
	 */
	public function findById(int $id): Season
	{
		if (!isset($this->store[$id])) {
			throw new EntityNotFoundException();
		}
		return $this->store[$id];
	}

	/**
	 * @param Season $season
	 */
	public function save(Season $season)
	{
		$this->storeEntity($season, $season->getId());
	}

	/**
	 * @param int $year
	 * @return Season
	 * @throws EntityNotFoundException
	 */
	public function findByYear(int $year): Season
	{
		/** @var Season $season */
		foreach ($this->store as $season) {
			if ($season->getYear() === $year) {
				return $season;
			}
		}
		throw new EntityNotFoundException();
	}
}