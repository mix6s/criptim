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
	/**
	 * @var float
	 */
	private $short;
	/**
	 * @var float
	 */
	private $long;
	/**
	 * @var float
	 */
	private $prevShort;
	/**
	 * @var float
	 */
	private $prevLong;
	/**
	 * @var \DateTimeImmutable
	 */
	private $timestamp;

	public function __construct(
		int $signal,
		float $short,
		float $long,
		float $prevShort,
		float $prevLong,
		\DateTimeImmutable $timestamp
	)
	{
		$this->signal = $signal;
		$this->short = $short;
		$this->long = $long;
		$this->prevShort = $prevShort;
		$this->prevLong = $prevLong;
		$this->timestamp = $timestamp;
	}

	public function signalIsLong(): bool
	{
		return $this->signal === self::SIGNAL_LONG;
	}

	public function signalIsNone(): bool
	{
		return $this->signal === self::SIGNAL_NONE;
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
		return $this->short;
	}

	public function getLongValue(): float
	{
		return $this->long;
	}

	public function getPrevShortValue(): float
	{
		return $this->prevShort;
	}

	public function getTimestamp(): \DateTimeImmutable
	{
		return $this->timestamp;
	}
}