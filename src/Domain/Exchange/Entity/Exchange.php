<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 14.01.2018
 * Time: 13:39
 */

namespace Domain\Exchange\Entity;


use Domain\Exchange\ValueObject\ExchangeId;

class Exchange
{
	/**
	 * @var ExchangeId
	 */
	private $id;

	public function __construct(ExchangeId $id)
	{
		$this->id = $id;
	}

	/**
	 * @return ExchangeId
	 */
	public function getId(): ExchangeId
	{
		return $this->id;
	}
}