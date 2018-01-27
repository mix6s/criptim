<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/27/18
 * Time: 5:09 PM
 */

namespace Domain\Exchange\UseCase\Request;


use Domain\Exchange\ValueObject\ExchangeId;

class SyncExchangeRequest
{
	/**
	 * @var ExchangeId
	 */
	private $exchangeId;

	/**
	 * @return ExchangeId
	 */
	public function getExchangeId(): ExchangeId
	{
		return $this->exchangeId;
	}

	/**
	 * @param ExchangeId $exchangeId
	 */
	public function setExchangeId(ExchangeId $exchangeId)
	{
		$this->exchangeId = $exchangeId;
	}
}