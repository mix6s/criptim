<?php
/**
 * Created by PhpStorm.
 * User: Mix6s
 * Date: 26.07.2017
 * Time: 8:24
 */

namespace Domain\Tests\Repository;

use Domain\Exception\EntityNotFoundException;


/**
 * Class InMemoryRepository
 * @package Domain\Tests\Repository
 */
trait InMemoryRepositoryTrait
{
	private $store = [];

	/**
	 * @param $entity
	 * @param $entityId
	 */
	public function storeEntity($entity, $entityId)
	{
		$this->store[(string)$entityId] = $entity;
	}

	/**
	 * @param $entityId
	 * @return bool
	 */
	public function entityExist($entityId): bool
	{
		return isset($this->store[(string)$entityId]);
	}

	public function getEntity($entityId)
	{
		if (!$this->entityExist($entityId)) {
			throw new EntityNotFoundException(sprintf('Entity with id %s not found in %s', $entityId, self::class));
		}
		return $this->store[(string)$entityId];
	}

	public function clear()
	{
		$this->store = [];
	}
}