<?php
/**
 * Created by PhpStorm.
 * User: Mix6s
 * Date: 26.07.2017
 * Time: 8:24
 */

namespace Domain\Tests\Repository;


/**
 * Class MemoryRepository
 * @package Domain\Tests\Repository
 */
trait InMemoryRepositoryTrait
{
	private $store = [];

	private $id = 0;

	/**
	 * @return int
	 */
	public function getNextId(): int
	{
		return $this->id++;
	}

	/**
	 * @param $entity
	 * @param $entityId
	 */
	public function storeEntity($entity, $entityId)
	{
		$this->store[$entityId] = $entity;
	}

	public function clear()
	{
		$this->store = [];
		$this->id = 0;
	}
}