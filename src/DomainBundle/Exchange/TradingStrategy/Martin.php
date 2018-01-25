<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/25/18
 * Time: 10:23 AM
 */

namespace DomainBundle\Exchange\TradingStrategy;


use Domain\Exchange\Entity\BotTradingSession;
use Domain\Exchange\Entity\TradingStrategyInterface;
use Domain\Exchange\ValueObject\TradingStrategyId;
use Domain\Exchange\ValueObject\TradingStrategySettings;

class Martin implements TradingStrategyInterface
{
	const ID = 'martin';

	/**
	 * @var TradingStrategyId
	 */
	private $id;

	public function __construct()
	{
		$this->id = new TradingStrategyId(self::ID);
	}

	public function getId(): TradingStrategyId
	{
		return $this->id;
	}

	public function isNeedToStartTrading(TradingStrategySettings $settings): bool
	{
		return true;
	}

	public function processTrading(BotTradingSession $session)
	{
		// TODO: Implement processTrading() method.
	}
}