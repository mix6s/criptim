<?php


namespace DomainBundle\Exchange\TradingStrategy;


use Domain\Exception\DomainException;
use Domain\Exception\EntityNotFoundException;
use Domain\Exchange\Entity\BotTradingSession;
use Domain\Exchange\Entity\TradingStrategyInterface;
use Domain\Exchange\Repository\BotRepositoryInterface;
use Domain\Exchange\Repository\BotTradingSessionAccountRepositoryInterface;
use Domain\Exchange\Repository\BotTradingSessionRepositoryInterface;
use Domain\Exchange\Repository\ExchangeRepositoryInterface;
use Domain\Exchange\Repository\OrderRepositoryInterface;
use Domain\Exchange\UseCase\CancelOrderUseCase;
use Domain\Exchange\UseCase\CreateOrderUseCase;
use Domain\Exchange\UseCase\GetBotTradingSessionBalancesUseCase;
use Domain\Exchange\UseCase\Request\CancelOrderRequest;
use Domain\Exchange\UseCase\Request\CreateOrderRequest;
use Domain\Exchange\UseCase\Request\GetBotTradingSessionBalancesRequest;
use Domain\Exchange\UseCase\Response\GetBotTradingSessionBalancesResponse;
use Domain\Exchange\ValueObject\TradingStrategyId;
use Domain\Exchange\ValueObject\TradingStrategySettings;
use Money\Currency;
use Money\CurrencyPair;
use Psr\Log\LoggerInterface;

class EmaWithMartin implements TradingStrategyInterface
{
	const ID = 'ema_with_martin';
	/**
	 * @var TradingStrategyId
	 */
	private $id;
	/**
	 * @var BotTradingSessionRepositoryInterface
	 */
	private $botTradingSessionRepository;
	/**
	 * @var BotTradingSessionAccountRepositoryInterface
	 */
	private $botTradingSessionAccountRepository;
	/**
	 * @var GetBotTradingSessionBalancesUseCase
	 */
	private $getBotTradingSessionBalancesUseCase;
	/**
	 * @var BotRepositoryInterface
	 */
	private $botRepository;
	/**
	 * @var ExchangeRepositoryInterface
	 */
	private $exchangeRepository;
	/**
	 * @var CreateOrderUseCase
	 */
	private $createOrderUseCase;
	/**
	 * @var OrderRepositoryInterface
	 */
	private $orderRepository;
	/**
	 * @var CancelOrderUseCase
	 */
	private $cancelOrderUseCase;
	/**
	 * @var LoggerInterface
	 */
	private $logger;
	/**
	 * @var Martin
	 */
	private $martin;

	public function __construct(
		BotTradingSessionRepositoryInterface $botTradingSessionRepository,
		BotTradingSessionAccountRepositoryInterface $botTradingSessionAccountRepository,
		GetBotTradingSessionBalancesUseCase $getBotTradingSessionBalancesUseCase,
		BotRepositoryInterface $botRepository,
		ExchangeRepositoryInterface $exchangeRepository,
		CreateOrderUseCase $createOrderUseCase,
		OrderRepositoryInterface $orderRepository,
		CancelOrderUseCase $cancelOrderUseCase,
		LoggerInterface $logger,
		Martin $martin
	)
	{
		$this->id = new TradingStrategyId(self::ID);
		$this->botTradingSessionRepository = $botTradingSessionRepository;
		$this->botTradingSessionAccountRepository = $botTradingSessionAccountRepository;
		$this->getBotTradingSessionBalancesUseCase = $getBotTradingSessionBalancesUseCase;
		$this->botRepository = $botRepository;
		$this->exchangeRepository = $exchangeRepository;
		$this->createOrderUseCase = $createOrderUseCase;
		$this->orderRepository = $orderRepository;
		$this->cancelOrderUseCase = $cancelOrderUseCase;
		$this->logger = $logger;
		$this->martin = $martin;
	}

	public function getId(): TradingStrategyId
	{
		return $this->id;
	}

	public function isNeedToStartTrading(TradingStrategySettings $settings): bool
	{
		$settings = $settings->getData();
		$period = new \DateInterval($settings['period']);
		$baseCurrency = new Currency($settings['baseCurrency'] ?? 'XRP');
		$quoteCurrency = new Currency($settings['quoteCurrency'] ?? 'BTC');
		$state = $this->getState($period, $baseCurrency, $quoteCurrency);
		return $state->signalIsLong();
	}

	public function processTrading(BotTradingSession $session)
	{
		$botId = $session->getBotId();
		$bot = $this->botRepository->findById($botId);
		$exchange = $this->exchangeRepository->findById($bot->getExchangeId());

		$settings = $session->getTradingStrategySettings()->getData();
		$period = new \DateInterval($settings['period']);
		$short = $settings['short'];
		$long = $settings['long'];
		$baseCurrency = new Currency($settings['baseCurrency'] ?? 'XRP');
		$quoteCurrency = new Currency($settings['quoteCurrency'] ?? 'BTC');
		$symbolString = $baseCurrency->getCode() . $quoteCurrency->getCode();
		$state = $this->getState($period, $baseCurrency, $quoteCurrency);

		$createOrderRequest = new CreateOrderRequest();
		$createOrderRequest->setBotTradingSessionId($session->getId());
		$createOrderRequest->setExchangeId($exchange->getId());
		$createOrderRequest->setSymbol(new CurrencyPair($baseCurrency, $quoteCurrency, 0));
		$cancelOrderRequest = new CancelOrderRequest();

		$balancesRequest = new GetBotTradingSessionBalancesRequest();
		$balancesRequest->setBotTradingSessionId($session->getId());

		$balancesRequest->setCurrency($baseCurrency);
		$baseCurrencyBalances = $this->getBotTradingSessionBalancesUseCase->execute($balancesRequest);
		$balancesRequest->setCurrency($quoteCurrency);
		$quoteCurrencyBalances = $this->getBotTradingSessionBalancesUseCase->execute($balancesRequest);

		$bidPrice = $exchange->getBid($symbolString);
		$askPrice = $exchange->getAsk($symbolString);

		switch ($state->getSignal()) {
			case EmaState::SIGNAL_LONG:
				break;
			case EmaState::SIGNAL_SHORT:
				break;
			case EmaState::SIGNAL_NONE:
				if ($state->getShortValue() < $state->getLongValue()) {
					$this->martin->processTrading($session);
					break;
				}
				if ($state->getShortValue() >= $state->getPrevShortValue()) {
					break;
				}
				/**
				5. If signal = 0
				and exist buy trade
				and bid price > buy price * (1 +  opt_profit_percent / 100.0)
				and short go down => sell buy bid price
				 */
				break;
			default:
				throw new DomainException(sprintf('Unknown signal %s', $state->getSignal()));
				break;
		}

		$balancesRequest->setCurrency($baseCurrency);
		$baseCurrencyBalances = $this->getBotTradingSessionBalancesUseCase->execute($balancesRequest);
		$balancesRequest->setCurrency($quoteCurrency);
		$quoteCurrencyBalances = $this->getBotTradingSessionBalancesUseCase->execute($balancesRequest);

		try {
			$lastSellOrder = $this->orderRepository->findLastSell($session->getId());
			if (!$baseCurrencyBalances->getAccountBalance()->isZero()) {
				return;
			}
		} catch (EntityNotFoundException $exception) {
			return;
		}
		$session->end();
		$this->logger->info(sprintf('Session #%s: end session', (string)$session->getId()), [
			'orderId' => (string)$lastSellOrder->getId(),
			'baseBalance' => $this->balancesAsArray($baseCurrencyBalances),
			'quoteBalance' => $this->balancesAsArray($quoteCurrencyBalances),
		]);

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

	private function getState(\DateInterval $period, Currency $baseCurrency, Currency $quoteCurrency): EmaState
	{
		return new EmaState(EmaState::SIGNAL_LONG);
	}

	private function balancesAsArray(GetBotTradingSessionBalancesResponse $response)
	{
		return [
			'currency' => $response->getBotTradingSessionAccount()->getCurrency()->getCode(),
			'start' => $response->getStartBalance()->getAmount(),
			'account' => $response->getAccountBalance()->getAmount(),
			'inOrder' => $response->getInOrdersBalance()->getAmount(),
			'available' => $response->getAccountBalance()->getAmount(),
		];
	}
}