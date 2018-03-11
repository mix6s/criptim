<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/27/18
 * Time: 7:15 PM
 */

namespace Domain\Exchange\Entity;


use Domain\Exchange\ValueObject\OrderId;

class ExchangeOrder
{
	/**
	 * @var OrderId
	 */
	private $id;
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
	 * @var float
	 */
	private $execAmount;
	/**
	 * @var string
	 */
	private $symbol;
	/**
	 * @var string
	 */
	private $status;

	public function __construct(
		OrderId $id,
		string $type = null,
		float $price = null,
		float $amount = null,
		float $execAmount = null,
		string $symbol = null,
		string $status = null
	)
	{

		$this->id = $id;
		$this->type = $type;
		$this->price = $price;
		$this->amount = $amount;
		$this->execAmount = $execAmount;
		$this->symbol = $symbol;
		$this->status = $status;
	}

	/**
	 * @return OrderId
	 */
	public function getId(): OrderId
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @return float
	 */
	public function getPrice()
	{
		return $this->price;
	}

	/**
	 * @return float
	 */
	public function getAmount()
	{
		return $this->amount;
	}

	/**
	 * @return float
	 */
	public function getExecAmount()
	{
		return $this->execAmount;
	}

	/**
	 * @return string
	 */
	public function getSymbol()
	{
		return $this->symbol;
	}

	/**
	 * @return string
	 */
	public function getStatus()
	{
		return $this->status;
	}

}