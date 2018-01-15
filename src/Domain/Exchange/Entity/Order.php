<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 15.01.2018
 * Time: 14:01
 */

namespace Domain\Exchange\Entity;


use Domain\Exchange\ValueObject\BotTradingSessionId;
use Domain\Exchange\ValueObject\OrderId;

class Order
{
	const STATUS_NEW = 'new';
	const STATUS_PARTIALLY_FILLED = 'partially_filled';
	const STATUS_FILLED = 'filled';
	const STATUS_CANCELED = 'canceled';

	/**
	 * @var OrderId
	 */
	private $id;
	/**
	 * @var BotTradingSessionId
	 */
	private $botTradingSessionId;
	/**
	 * @var string
	 */
	private $type;
	/**
	 * @var float
	 */
	private $price;
	/**
	 * @var float
	 */
	private $amount;
	/**
	 * @var string
	 */
	private $symbol;
	/**
	 * @var string
	 */
	private $status;
	/**
	 * @var float
	 */
	private $execAmount;

	public function __construct(OrderId $id, BotTradingSessionId $botTradingSessionId, string $type, float $price, float $amount, string $symbol)
	{
		$this->id = $id;
		$this->botTradingSessionId = $botTradingSessionId;
		$this->type = $type;
		$this->price = $price;
		$this->amount = $amount;
		$this->symbol = $symbol;
		$this->status = self::STATUS_NEW;
		$this->execAmount = 0.;
	}
}