<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 20.02.2018
 * Time: 22:26
 */

namespace DomainBundle\Exchange\TradingStrategy;


class EmaState
{
	const SIGNAL_SHORT = -1;
	const SIGNAL_LONG = 1;
	const SIGNAL_NONE = 0;
	/**
	 * @var int
	 */
	private $signal;

	public function __construct(int $signal)
	{
		$this->signal = $signal;
	}

	public function signalIsLong(): bool
	{
		return $this->signal === self::SIGNAL_LONG;
	}
	/**
	 * @return int
	 */
	public function getSignal(): int
	{
		return $this->signal;
	}

	public function getShortValue(): float
	{

	}

	public function getLongValue(): float
	{

	}

	public function getPrevShortValue(): float
	{
	}

	public function getTimestamp(): \DateTimeImmutable
	{
	}
}