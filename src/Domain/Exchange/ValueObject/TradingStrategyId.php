<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 14.01.2018
 * Time: 13:05
 */

namespace Domain\Exchange\ValueObject;


use Domain\ValueObject\Id;

class TradingStrategyId extends Id
{
	const EMA = 'ema';

	private static $ids = [];

	public static function EMA()
	{
		return self::resolve(self::EMA);
	}

	private static function resolve(string $id): TradingStrategyId
	{
		return self::$ids[$id] ?? self::$ids[$id] = new TradingStrategyId($id);
	}
}