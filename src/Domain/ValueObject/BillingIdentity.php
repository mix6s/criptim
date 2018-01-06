<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 07.01.2018
 * Time: 0:26
 */

namespace Domain\ValueObject;


class BillingIdentity
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