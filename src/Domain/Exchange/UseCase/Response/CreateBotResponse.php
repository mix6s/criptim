<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/25/18
 * Time: 3:02 PM
 */

namespace Domain\Exchange\UseCase\Response;


use Domain\Exchange\Entity\Bot;

class CreateBotResponse
{
	/**
	 * @var Bot
	 */
	private $bot;

	public function __construct(Bot $bot)
	{
		$this->bot = $bot;
	}

	/**
	 * @return Bot
	 */
	public function getBot(): Bot
	{
		return $this->bot;
	}
}