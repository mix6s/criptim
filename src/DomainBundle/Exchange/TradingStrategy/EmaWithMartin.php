<?php


namespace DomainBundle\Exchange\TradingStrategy;


use Domain\Exception\DomainException;
use Domain\Exception\EntityNotFoundException;
use Domain\Exception\InsufficientFundsException;
use Domain\Exchange\Entity\Bot;
use Domain\Exchange\Entity\BotTradingSession;
use Domain\Exchange\Entity\ExchangeInterface;
use Domain\Exchange\Entity\TradingStrategyInterface;
use Domain\Exchange\Policy\MoneyFromFloatPolicy;
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
use Domain\Exchange\ValueObject\BotTradingSessionId;
use Domain\Exchange\ValueObject\TradingStrategyId;
use Domain\Exchange\ValueObject\TradingStrategySettings;
use Domain\Policy\DomainCurrenciesPolicy;
use DomainBundle\Exchange\Policy\CryptoMoneyFormatter;
use Money\Currency;
use Money\CurrencyPair;
use Money\Money;
use Psr\Log\LoggerInterface;

class EmaWithMartin implements TradingStrategyInterface
{
	const ID = 'ema_with_martin';
	const BUY_MULTIPLIER = 1;
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
	private $currencies;
	private $formatter;
	private $moneyFromFloatPolicy;

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
		$this->formatter = new CryptoMoneyFormatter();
		$this->currencies = new DomainCurrenciesPolicy();
		$this->moneyFromFloatPolicy = new MoneyFromFloatPolicy();
	}

	public function getId(): TradingStrategyId
	{
		return $this->id;
	}

	public function isNeedToStartTrading(Bot $bot): bool
	{
		$settings = $bot->getTradingStrategySettings()->getData();
		$period = new \DateInterval($settings['period']);
		$baseCurrency = new Currency($settings['baseCurrency'] ?? 'XRP');
		$quoteCurrency = new Currency($settings['quoteCurrency'] ?? 'BTC');
		$short = $settings['short'];
		$long = $settings['long'];
		$exchange = $this->exchangeRepository->findById($bot->getExchangeId());
		$state = $this->getState($exchange, $period, $baseCurrency, $quoteCurrency, $short, $long);
		$this->logger->info(sprintf('Bot #%s: state data', (string)$bot->getId()), [
			'signal' => $state->getSignal(),
			'short' => $state->getShortValue(),
			'long' => $state->getLongValue(),
			'prev_short' => $state->getPrevShortValue(),
			'timestamp' => $state->getTimestamp()->format(DATE_RFC3339),
		]);
		if ($state->signalIsLong()) {
			return true;
		}
		if ($state->signalIsNone() && $state->getShortValue() < $state->getLongValue()) {
			return true;
		}
		return false;
	}

	public function processTrading(BotTradingSession $session)
	{
		$botId = $session->getBotId();
		$bot = $this->botRepository->findById($botId);
		$exchange = $this->exchangeRepository->findById($bot->getExchangeId());

		$settings = $session->getTradingStrategySettings()->getData();
		$period = new \DateInterval($settings['period']);
		$orderRecreateSeconds = $settings['order_recreate'] ?? 30;
		$short = $settings['short'];
		$long = $settings['long'];
		$goDownProfitPercent = $settings['go_down_profit_percent'] ?? 0.4;
		$baseCurrency = new Currency($settings['baseCurrency'] ?? 'XRP');
		$quoteCurrency = new Currency($settings['quoteCurrency'] ?? 'BTC');
		$symbolString = $baseCurrency->getCode() . $quoteCurrency->getCode();
		$minBalance = new Money(0, $baseCurrency);
		$amountInc = $this->moneyFromFloatPolicy->getMoney($baseCurrency, $exchange->getAmountIncrement($symbolString));
		$priceTickSize = $this->moneyFromFloatPolicy->getMoney($quoteCurrency, $exchange->getPriceTickSize($symbolString));
		$state = $this->getState($exchange, $period, $baseCurrency, $quoteCurrency, $short, $long);
		$this->logger->info(sprintf('SessionEma #%s: state data', (string)$session->getId()), [
			'signal' => $state->getSignal(),
			'short' => $state->getShortValue(),
			'long' => $state->getLongValue(),
			'prev_short' => $state->getPrevShortValue(),
			'timestamp' => $state->getTimestamp()->format(DATE_RFC3339),
		]);

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
				if (!$this->isTimeToRecreateActiveOrder($session->getId(), 'buy', $orderRecreateSeconds)) {
					return;
				}
				$activeOrders = $this->orderRepository->findActive($session->getId());
				foreach ($activeOrders as $order) {
					$cancelOrderRequest->setOrderId($order->getId());
					$this->cancelOrderUseCase->execute($cancelOrderRequest);
					$this->logger->info(sprintf('SessionEma #%s: cancel order', (string)$session->getId()), [
						'orderId' => (string)$cancelOrderRequest->getOrderId(),
						'baseBalance' => $this->balancesAsArray($baseCurrencyBalances),
						'quoteBalance' => $this->balancesAsArray($quoteCurrencyBalances),
					]);
				}

				$balancesRequest->setCurrency($baseCurrency);
				$baseCurrencyBalances = $this->getBotTradingSessionBalancesUseCase->execute($balancesRequest);
				$balancesRequest->setCurrency($quoteCurrency);
				$quoteCurrencyBalances = $this->getBotTradingSessionBalancesUseCase->execute($balancesRequest);

				$now = new \DateTimeImmutable();
				$secondsPeriod = (new \DateTimeImmutable('@0'))->add($period)->getTimestamp();
				$secondsDelta = $now->getTimestamp() - $state->getTimestamp()->getTimestamp() - $secondsPeriod;
				$priceDelta = $askPrice - $state->getShortValue();
				$buyPriceFloat = $state->getShortValue() + $secondsDelta / ($secondsPeriod / $priceDelta);

				$buyPrice = $this->formatter->format(
					$this->round($this->moneyFromFloatPolicy->getMoney($quoteCurrency, $buyPriceFloat), $priceTickSize)
				);
				$amount = (new Money($quoteCurrencyBalances->getAvailableBalance()->getAmount(), $baseCurrency))
					->divide(self::BUY_MULTIPLIER + $exchange->getFee())
					->divide($buyPrice);
				$buyAmount = $this->round($amount, $amountInc);

				if ($buyAmount->lessThan($amountInc)) {
					return;
				}

				$createOrderRequest->setType('buy');
				$createOrderRequest->setAmount($this->formatter->format($buyAmount));
				$createOrderRequest->setPrice($buyPrice);
				try {
					$lastBuyOrder = $this->createOrderUseCase->execute($createOrderRequest)->getOrder();
				} catch (InsufficientFundsException $exception) {
					$this->logger->error(sprintf('SessionEma #%s: buy order error InsufficientFundsException', (string)$session->getId()));
					return;
				}
				$this->logger->info(sprintf('SessionEma #%s: buy order created', (string)$session->getId()), [
					'orderId' => (string)$lastBuyOrder->getId(),
					'baseBalance' => $this->balancesAsArray($baseCurrencyBalances),
					'quoteBalance' => $this->balancesAsArray($quoteCurrencyBalances),
				]);
				return;
			case EmaState::SIGNAL_SHORT:
				$activeOrders = $this->orderRepository->findActive($session->getId());
				$lastSellOrder = null;
				foreach ($activeOrders as $order) {
					if ($order->getType() !== 'sell') {
						continue;
					}
					if (!$lastSellOrder) {
						$lastSellOrder = $order;
					}
					if ($lastSellOrder->getCreatedAt()->getTimestamp() <= $order->getCreatedAt()->getTimestamp()) {
						$lastSellOrder = $order;
					}
				}
				if ($lastSellOrder && (time() - $lastSellOrder->getCreatedAt()->getTimestamp()) < $orderRecreateSeconds) {
					break;
				}
				foreach ($activeOrders as $order) {
					$cancelOrderRequest->setOrderId($order->getId());
					$this->cancelOrderUseCase->execute($cancelOrderRequest);
					$this->logger->info(sprintf('SessionEma #%s: cancel order', (string)$session->getId()), [
						'orderId' => (string)$cancelOrderRequest->getOrderId(),
						'baseBalance' => $this->balancesAsArray($baseCurrencyBalances),
						'quoteBalance' => $this->balancesAsArray($quoteCurrencyBalances),
					]);
				}

				$balancesRequest->setCurrency($baseCurrency);
				$baseCurrencyBalances = $this->getBotTradingSessionBalancesUseCase->execute($balancesRequest);
				$balancesRequest->setCurrency($quoteCurrency);
				$quoteCurrencyBalances = $this->getBotTradingSessionBalancesUseCase->execute($balancesRequest);

				$sellAmount = $baseCurrencyBalances->getAvailableBalance();
				if ($sellAmount->lessThan($amountInc)) {
					break;
				}

				$createOrderRequest->setAmount($this->formatter->format($sellAmount));
				$createOrderRequest->setPrice($bidPrice);
				$createOrderRequest->setType('sell');
				try {
					$lastSellOrder = $this->createOrderUseCase->execute($createOrderRequest)->getOrder();
				} catch (InsufficientFundsException $exception) {
					$this->logger->warning(sprintf('SessionEma #%s: sell order error Insufficient Funds Exception', (string)$session->getId()));
					break;
				}
				$this->logger->info(sprintf('SessionEma #%s: sell order created', (string)$session->getId()), [
					'orderId' => (string)$lastSellOrder->getId(),
					'amount' => $sellAmount,
					'price' => $bidPrice,
					'baseBalance' => $this->balancesAsArray($baseCurrencyBalances),
					'quoteBalance' => $this->balancesAsArray($quoteCurrencyBalances),
				]);


				break;
			case EmaState::SIGNAL_NONE:
				if ($state->getShortValue() < $state->getLongValue()) {
					$this->martin->processTrading($session);
					return;
				}

				if ($state->getShortValue() >= $state->getPrevShortValue()) {
					break;
				}

				$amountSum = 0;
				$total = 0;
				$buyOrders = $this->orderRepository->findBySessionIdAndType($session->getId(), 'buy');
				foreach ($buyOrders as $order) {
					if ($order->getExecAmount() === 0) {
						continue;
					}
					$total += $order->getExecAmount() * $order->getPrice();
					$amountSum += $order->getExecAmount();
				}
				$sellPrice = $this->moneyFromFloatPolicy->getMoney($quoteCurrency, $total)->divide($amountSum)->multiply(1 + $goDownProfitPercent / 100);
				$bidMoney = $this->moneyFromFloatPolicy->getMoney($quoteCurrency, $bidPrice);
				if ($sellPrice->greaterThanOrEqual($bidMoney)) {
					break;
				}

				$activeOrders = $this->orderRepository->findActive($session->getId());
				$lastSellOrder = null;
				foreach ($activeOrders as $order) {
					if ($order->getType() !== 'sell') {
						continue;
					}
					if (!$lastSellOrder) {
						$lastSellOrder = $order;
					}
					if ($lastSellOrder->getCreatedAt()->getTimestamp() <= $order->getCreatedAt()->getTimestamp()) {
						$lastSellOrder = $order;
					}
				}
				if ($lastSellOrder && (time() - $lastSellOrder->getCreatedAt()->getTimestamp()) < $orderRecreateSeconds) {
					break;
				}
				foreach ($activeOrders as $order) {
					$cancelOrderRequest->setOrderId($order->getId());
					$this->cancelOrderUseCase->execute($cancelOrderRequest);
					$this->logger->info(sprintf('SessionEma #%s: cancel order', (string)$session->getId()), [
						'orderId' => (string)$cancelOrderRequest->getOrderId(),
						'baseBalance' => $this->balancesAsArray($baseCurrencyBalances),
						'quoteBalance' => $this->balancesAsArray($quoteCurrencyBalances),
					]);
				}

				$balancesRequest->setCurrency($baseCurrency);
				$baseCurrencyBalances = $this->getBotTradingSessionBalancesUseCase->execute($balancesRequest);
				$balancesRequest->setCurrency($quoteCurrency);
				$quoteCurrencyBalances = $this->getBotTradingSessionBalancesUseCase->execute($balancesRequest);

				$sellAmount = $baseCurrencyBalances->getAvailableBalance();
				if ($sellAmount->lessThan($amountInc)) {
					break;
				}

				$createOrderRequest->setAmount($this->formatter->format($sellAmount));
				$createOrderRequest->setPrice($this->formatter->format($bidMoney));
				$createOrderRequest->setType('sell');
				try {
					$lastSellOrder = $this->createOrderUseCase->execute($createOrderRequest)->getOrder();
				} catch (InsufficientFundsException $exception) {
					$this->logger->warning(sprintf('SessionEma #%s: sell order error Insufficient Funds Exception', (string)$session->getId()));
					break;
				}
				$this->logger->info(sprintf('SessionEma #%s: sell order created', (string)$session->getId()), [
					'orderId' => (string)$lastSellOrder->getId(),
					'amount' => $sellAmount,
					'price' => $this->formatter->format($bidMoney),
					'baseBalance' => $this->balancesAsArray($baseCurrencyBalances),
					'quoteBalance' => $this->balancesAsArray($quoteCurrencyBalances),
				]);
				break;
			default:
				throw new DomainException(sprintf('Unknown signal %s', $state->getSignal()));
				break;
		}

		try {
			$lastSellOrder = $this->orderRepository->findLastSell($session->getId());
		} catch (EntityNotFoundException $exception) {
			return;
		}
		$activeOrders = $this->orderRepository->findActive($session->getId());
		foreach ($activeOrders as $order) {
			$cancelOrderRequest->setOrderId($order->getId());
			$this->cancelOrderUseCase->execute($cancelOrderRequest);
			$this->logger->info(sprintf('SessionEma #%s: cancel order', (string)$session->getId()), [
				'orderId' => (string)$cancelOrderRequest->getOrderId(),
				'baseBalance' => $this->balancesAsArray($baseCurrencyBalances),
				'quoteBalance' => $this->balancesAsArray($quoteCurrencyBalances),
			]);
		}
		$balancesRequest->setCurrency($baseCurrency);
		$baseCurrencyBalances = $this->getBotTradingSessionBalancesUseCase->execute($balancesRequest);
		$balancesRequest->setCurrency($quoteCurrency);
		$quoteCurrencyBalances = $this->getBotTradingSessionBalancesUseCase->execute($balancesRequest);
		if (!$baseCurrencyBalances->getAccountBalance()->isZero()) {
			return;
		}
		$session->end();
		$this->logger->info(sprintf('Session #%s: end session', (string)$session->getId()), [
			'orderId' => (string)$lastSellOrder->getId(),
			'baseBalance' => $this->balancesAsArray($baseCurrencyBalances),
			'quoteBalance' => $this->balancesAsArray($quoteCurrencyBalances),
		]);
	}

	private function isTimeToRecreateActiveOrder(BotTradingSessionId $id, string $orderType, int $orderRecreateSeconds)
	{
		$activeOrders = $this->orderRepository->findActive($id);
		$lastSellOrder = null;
		foreach ($activeOrders as $order) {
			if ($order->getType() !== $orderType) {
				continue;
			}
			if (!$lastSellOrder) {
				$lastSellOrder = $order;
			}
			if ($lastSellOrder->getCreatedAt()->getTimestamp() <= $order->getCreatedAt()->getTimestamp()) {
				$lastSellOrder = $order;
			}
		}
		if ($lastSellOrder && (time() - $lastSellOrder->getCreatedAt()->getTimestamp()) < $orderRecreateSeconds) {
			return false;
		}
		return true;
	}

	private function getState(ExchangeInterface $exchange, \DateInterval $period, Currency $baseCurrency, Currency $quoteCurrency, int $short, int $long): EmaState
	{
		$candles = $exchange->getCandles($baseCurrency, $quoteCurrency, $period, $long * 2 + 1);
		$data = [
			'prices' => [],
			'timestamps' => []
		];

		$count = 0;
		foreach ($candles as $candle) {
			$count++;
			if ($count >= count($candles)) {
				break;
			}
			$data['prices'][] = (float)$candle['close'];
			$data['timestamps'][] = new \DateTimeImmutable($candle['timestamp']);
		}
		$index = 0;
		$shortSmaValue = 0;
        $longSmaValue = 0;
        $shortEmaValue = 0;
        $longEmaValue = 0;
		$prevShortEma = 0;
        $prevLongEma = 0;
        $shortEma = [];
        $longEma = [];
		$shortSma = [];
		$longSma = [];
		$signal = 0;
		foreach ($data['timestamps'] as $timestamp) {
			if ($index >= $short) {
				$prevShortEma = $shortEmaValue;
				if ($prevShortEma == 0) {
					$prevShortEma = $shortSmaValue;
				}
				$shortEmaValue = $this->EMA(array_slice($data['prices'], $index - $short + 1, $short), $prevShortEma);
			}

			if ($index >= $long) {
				$prevLongEma = $longEmaValue;
				if ($prevLongEma == 0) {
					$prevLongEma = $longSmaValue;
				}
				$longEmaValue = $this->EMA(array_slice($data['prices'], $index - $long + 1, $long), $prevLongEma);
			}

			$shortEma[] = $shortEmaValue;
			$longEma[] = $longEmaValue;

			if ($index >= $short - 1) {
				$shortSmaValue = $this->SMA(array_slice($data['prices'], $index - $short + 1, $short));
			}
			if ($index >= $long - 1) {
				$longSmaValue = $this->SMA(array_slice($data['prices'], $index - $long + 1, $long));
			}
			$shortSma[] = $shortSmaValue;
			$longSma[] = $longSmaValue;

			$signal = EmaState::SIGNAL_NONE;
			if ($shortEmaValue > $longEmaValue && $prevShortEma < $prevLongEma) {
				$signal = EmaState::SIGNAL_LONG;
			} elseif ($shortEmaValue < $longEmaValue && $prevShortEma > $prevLongEma) {
				$signal = EmaState::SIGNAL_SHORT;
			}
			if ($shortEmaValue < $longEmaValue) {
				//$signal = EmaState::SIGNAL_SHORT;
			}
			$index++;
		}
		return new EmaState($signal, $shortEmaValue, $longEmaValue, $prevShortEma, $prevLongEma, $data['timestamps'][count($data['timestamps']) - 1]);
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

	private function round(Money $money, Money $rounding): Money
	{
		return new Money(floor($money->getAmount() / $rounding->getAmount()) * $rounding->getAmount(),
			$money->getCurrency());
	}

	private function SMA(array $prices)
	{
		$totalClosing = array_sum($prices);
        return $totalClosing / count($prices);
	}

	private function EMA(array $prices, $prevEma)
	{
		$period = count($prices);
        $constant = (2 / ($period + 1));
        return ($prices[$period - 1] * $constant) + ($prevEma * (1 - $constant));
	}
}