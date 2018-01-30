<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 29.01.2018
 * Time: 22:49
 */

namespace Domain\Exchange\UseCase\Request;


use Domain\Exchange\ValueObject\OrderId;

class CancelOrderRequest
{
	/**
	 * @var OrderId
	 */
	private $orderId;

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
}