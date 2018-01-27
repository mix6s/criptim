<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/26/18
 * Time: 8:22 PM
 */

namespace Domain\Exchange\UseCase\Response;


use Domain\Exchange\Entity\Order;

class CreateOrderResponse
{
	/**
	 * @var Order
	 */
	private $order;

	public function __construct(Order $order)
	{
		$this->order = $order;
	}

	/**
	 * @return Order
	 */
	public function getOrder(): Order
	{
		return $this->order;
	}
}