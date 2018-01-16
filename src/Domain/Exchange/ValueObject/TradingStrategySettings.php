<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 13.01.2018
 * Time: 21:52
 */

namespace Domain\Exchange\ValueObject;


class TradingStrategySettings
{
	/**
	 * @var array
	 */
	private $data;

	public function __construct(array $data)
	{
		$this->data = $data;
	}

	/**
	 * @return array
	 */
	public function getData(): array
	{
		return $this->data;
	}
}