<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 13.01.2018
 * Time: 18:08
 */

namespace Domain\ValueObject;


class Id
{
	/**
	 * @var string
	 */
	private $id;

	public function __construct(string $id)
	{

		$this->id = $id;
	}

	public function __toString()
	{
		return $this->id;
	}

	public function isEmpty(): bool
	{
		return empty($this->id);
	}
}