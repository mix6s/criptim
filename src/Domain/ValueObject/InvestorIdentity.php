<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 05.01.2018
 * Time: 21:44
 */

namespace Domain\ValueObject;


class InvestorIdentity
{
	private $id;

	public function __construct(string $id)
	{
		$this->id = $id;
	}

	public function __toString(): string
	{
		return $this->id;
	}
}