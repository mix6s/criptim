<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 07.01.2018
 * Time: 18:22
 */

namespace Domain\ValueObject;


class InvestorAccountTransactionIdentity
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