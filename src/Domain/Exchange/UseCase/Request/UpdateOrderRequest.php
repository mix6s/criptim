<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 29.01.2018
 * Time: 21:16
 */

namespace Domain\Exchange\UseCase\Request;


use Domain\Exchange\Entity\ExchangeOrder;
use Domain\Exchange\ValueObject\OrderId;

class UpdateOrderRequest
{
	/** @var  OrderId */
	private $orderId;
	/** @var  ExchangeOrder */
	private $exchangeOrder;

	/**
	 * @return OrderId
	 */
	public function getOrderId(): OrderId
	{
		return $this->orderId;
	}

	/**
	 * @param OrderId $orderId
	 */
	public function setOrderId(OrderId $orderId)
	{
		$this->orderId = $orderId;
	}

	/**
	 * @return ExchangeOrder
	 */
	public function getExchangeOrder(): ExchangeOrder
	{
		return $this->exchangeOrder;
	}

	/**
	 * @param ExchangeOrder $exchangeOrder
	 */
	public function setExchangeOrder(ExchangeOrder $exchangeOrder)
	{
		$this->exchangeOrder = $exchangeOrder;
	}

}