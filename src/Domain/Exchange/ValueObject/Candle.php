<?php


namespace Domain\Exchange\ValueObject;


class Candle
{
	/**
	 * @var float
	 */
	private $close;
	/**
	 * @var \DateTimeInterface
	 */
	private $timestamp;

	public function __construct(
		float $close,
		\DateTimeInterface $timestamp
	)
	{
		$this->close = $close;
		$this->timestamp = $timestamp;
	}

	public function getClose(): float
	{
		return $this->close;
	}

	public function getTimestamp(): \DateTimeInterface
	{
		return $this->timestamp;
	}
}