<?php
/**
 * Created by PhpStorm.
 * User: Mix6s
 * Date: 27.07.2017
 * Time: 17:40
 */

namespace DomainBundle;


use Domain\ContainerInterface;
use Domain\Repository\SeasonRepositoryInterface;

class Container implements ContainerInterface
{
	private $container;

	public function __construct(\Symfony\Component\DependencyInjection\ContainerInterface $container)
	{
		$this->container = $container;
	}

	/**
	 * @return SeasonRepositoryInterface
	 */
	public function getSeasonRepository(): SeasonRepositoryInterface
	{
		// TODO: Implement getSeasonRepository() method.
	}
}