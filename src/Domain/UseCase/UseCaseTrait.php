<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 25.07.2017
 * Time: 19:40
 */

namespace Domain\UseCase;


use Domain\ContainerInterface;

/**
 * Trait UseCaseTrait
 * @package Domain\UseCase
 */
trait UseCaseTrait
{
	private $container;

	/**
	 * UseCaseTrait constructor.
	 * @param ContainerInterface $container
	 */
	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
	}

	/**
	 * @return ContainerInterface
	 */
	public function getContainer(): ContainerInterface
	{
		return $this->container;
	}
}