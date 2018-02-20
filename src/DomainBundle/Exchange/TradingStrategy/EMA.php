<?php


namespace DomainBundle\Exchange\TradingStrategy;


use Domain\Exchange\Entity\BotTradingSession;
use Domain\Exchange\Entity\TradingStrategyInterface;
use Domain\Exchange\ValueObject\TradingStrategyId;
use Domain\Exchange\ValueObject\TradingStrategySettings;

class EMA implements TradingStrategyInterface
{
	const ID = 'ema';
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

	}

	public function processTrading(BotTradingSession $session)
	{
		/**
		 *	1. Get signal from candles ()

		    signal = 0
			if (short_ema_value > long_ema_value) and (prev_short_ema < prev_long_ema):
			signal = 1
			elif (short_ema_value < long_ema_value) and (prev_short_ema > prev_long_ema):
			signal = -1
			if short_ema_value < long_ema_value:
			signal = -1

		 *	2. Cancel all active orders
		 * 	3. If signal = -1 => Sell all by ask price
		 * 	4. If signal = 1 => Buy by buy price where buy price:

			t = time.time()
			delta_t = float(self.options['interval']) * 60.
			delta_price = bid_price - self.strategy.get_short()
			buy_price = (t - self.strategy.get_timestamp() - delta_t) / (delta_t / delta_price) + self.strategy.get_short()
			self.buy(session, buy_price)

		 *	5. If signal = 0
		 * 	and exist buy trade
		 * 	and bid price > buy price * (1 +  opt_profit_percent / 100.0)
		 * 	and short go down => sell buy bid price

		 	last_buy_trade = self.session_manager.find_session_last_trade(session, 'buy')
			if last_buy_trade is not None:
			buy_price = float(last_buy_trade['price'])
			if bid_price > buy_price + buy_price * 0.4 / 100.0 and self.strategy.short_go_down():
			amount = self.sell(session, bid_price)
			if amount is not None:
			self.log_tg("Short go down, selling")

		 * 	6. If base amount is zero and last sell session order is filled (we sell all amount) => end session
		 */
	}
}