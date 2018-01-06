<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 06.01.2018
 * Time: 1:20
 */

namespace Domain\UseCase\CreateInvestor;


use Domain\Entity\Investor;

class Response
{
	private $investor;

	public function __construct(Investor $investor)
	{
		$this->investor = $investor;
	}

	public function getInvestor(): Investor
	{
		return $this->investor;
	}
}