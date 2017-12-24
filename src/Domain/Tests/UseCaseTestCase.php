<?php
/**
 * Created by PhpStorm.
 * User: Mix6s
 * Date: 26.07.2017
 * Time: 8:09
 */

namespace Domain\Tests;


use PHPUnit\Framework\TestCase;

/**
 * Class UseCaseTestCase
 * @package Domain\Tests
 */
class UseCaseTestCase extends TestCase
{
	private $container;

	public function setUp()
	{
		$this->container = new Container();
	}

	/**
	 * @return Container
	 */
	public function getContainer()
	{
		return $this->container;
	}
}