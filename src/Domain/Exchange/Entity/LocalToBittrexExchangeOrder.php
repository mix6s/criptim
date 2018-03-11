<?php


namespace Domain\Exchange\Entity;


use Domain\Exchange\ValueObject\BittrexOrderId;
use Domain\Exchange\ValueObject\OrderId;
use Domain\Exchange\ValueObject\LocalToBittrexExchangeOrderId;

class LocalToBittrexExchangeOrder
{

	/**
	 * @var LocalToBittrexExchangeOrderId
	 */
	private $id;
	/**
	 * @var OrderId
	 */
	private $orderId;
	/**
	 * @var BittrexOrderId
	 */
	private $bittrexOrderId;

	public function __construct(
		LocalToBittrexExchangeOrderId $id,
		OrderId $orderId,
		BittrexOrderId $bittrexOrderId
	)
	{
		$this->id = $id;
		$this->orderId = $orderId;
		$this->bittrexOrderId = $bittrexOrderId;
	}

	/**
	 * @return LocalToBittrexExchangeOrderId
	 */
	public function getId(): LocalToBittrexExchangeOrderId
	{
		return $this->id;
	}

	/**
	 * @return OrderId
	 */
	public function getOrderId(): OrderId
	{
		return $this->orderId;
	}

	/**
	 * @return BittrexOrderId
	 */
	public function getBittrexOrderId(): BittrexOrderId
	{
		return $this->bittrexOrderId;
	}
}