<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 07.01.2018
 * Time: 18:16
 */

namespace Domain\ValueObject;


class InvestorAccountIdentity
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