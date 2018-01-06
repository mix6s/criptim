<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 05.01.2018
 * Time: 22:44
 */

namespace Domain\ValueObject;


class DepositPayMethod
{
	const METHOD_CC = 'cc';
	/**
	 * @var string
	 */
	private $method;

	private function __construct(string $method)
	{
		$this->method = $method;
	}

	public static function cc(): DepositPayMethod
	{
		return new self(self::METHOD_CC);
	}

	public function __toString()
	{
		return $this->method;
	}
}