<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 05.01.2018
 * Time: 21:19
 */

namespace Domain\Entity;


use Domain\ValueObject\InvestorIdentity;

class Investor
{
	/**
	 * @var InvestorIdentity
	 */
	private $id;

	public function __construct(InvestorIdentity $id)
	{
		$this->id = $id;
	}

	/**
	 * @return InvestorIdentity
	 */
	public function getId(): InvestorIdentity
	{
		return $this->id;
	}
}