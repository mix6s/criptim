<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 14.01.2018
 * Time: 13:39
 */

namespace Domain\Exchange\Entity;


use Domain\Exchange\ValueObject\ExchangeId;

interface ExchangeInterface
{
	/**
	 * @return ExchangeId
	 */
	public function getId(): ExchangeId;
}