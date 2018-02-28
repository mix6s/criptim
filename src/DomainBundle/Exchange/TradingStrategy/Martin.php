<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/25/18
 * Time: 10:23 AM
 */

namespace DomainBundle\Exchange\TradingStrategy;


use Domain\Exception\EntityNotFoundException;
use Domain\Exception\InsufficientFundsException;
use Domain\Exchange\Entity\Bot;
use Domain\Exchange\Entity\BotTradingSession;
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
use Domain\Exchange\ValueObject\TradingStrategyId;
use Domain\Exchange\ValueObject\TradingStrategySettings;
use Domain\Policy\DomainCurrenciesPolicy;
use DomainBundle\Exchange\Policy\CryptoMoneyFormatter;
use Money\Currency;
use Money\CurrencyPair;
use Money\Money;
use Money\Number;
use Psr\Log\LoggerInterface;

class Martin implements TradingStrategyInterface
{
	const ID = 'martin';

	/**
	 * @var TradingStrategyId
	 */
	private $id;
	/**
	 * @var BotTradingSessionAccountRepositoryInterface
	 */
	private $botTradingSessionAccountRepository;
	/**
	 * @var BotTradingSessionRepositoryInterface
	 */
	private $botTradingSessionRepository;
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
	 * @var GetBotTradingSessionBalancesUseCase
	 */
	private $getBotTradingSessionBalancesUseCase;
	/**
	 * @var OrderRepositoryInterface
	 */
	private $orderRepository;
	private $formatter;
	private $currencies;
	/**
	 * @var CancelOrderUseCase
	 */
	private $cancelOrderUseCase;
	/**
	 * @var MoneyFromFloatPolicy
	 */
	private $moneyFromFloatPolicy;
	/**
	 * @var LoggerInterface
	 */
	private $logger;

	public function __construct(
		BotTradingSessionRepositoryInterface $botTradingSessionRepository,
		BotTradingSessionAccountRepositoryInterface $botTradingSessionAccountRepository,
		GetBotTradingSessionBalancesUseCase $getBotTradingSessionBalancesUseCase,
		BotRepositoryInterface $botRepository,
		ExchangeRepositoryInterface $exchangeRepository,
		CreateOrderUseCase $createOrderUseCase,
		OrderRepositoryInterface $orderRepository,
		CancelOrderUseCase $cancelOrderUseCase,
		LoggerInterface $logger
	) {
		$this->id = new TradingStrategyId(self::ID);
		$this->botTradingSessionAccountRepository = $botTradingSessionAccountRepository;
		$this->botTradingSessionRepository = $botTradingSessionRepository;
		$this->botRepository = $botRepository;
		$this->exchangeRepository = $exchangeRepository;
		$this->createOrderUseCase = $createOrderUseCase;
		$this->getBotTradingSessionBalancesUseCase = $getBotTradingSessionBalancesUseCase;
		$this->orderRepository = $orderRepository;
		$this->formatter = new CryptoMoneyFormatter();
		$this->currencies = new DomainCurrenciesPolicy();
		$this->cancelOrderUseCase = $cancelOrderUseCase;
		$this->moneyFromFloatPolicy = new MoneyFromFloatPolicy();
		$this->logger = $logger;
	}

	public function getId(): TradingStrategyId
	{
		return $this->id;
	}

	public function isNeedToStartTrading(Bot $bot): bool
	{
		return true;
	}

	public function processTrading(BotTradingSession $session)
	{
		$botId = $session->getBotId();
		$bot = $this->botRepository->findById($botId);
		$exchange = $this->exchangeRepository->findById($bot->getExchangeId());

		$settings = $session->getTradingStrategySettings()->getData();
		$buyOrderLimit = $settings['buy_order_limit'] ?? 1;
		$profitPercent = $settings['profit_percent'] ?? 0.3;
		$priceDecPercent = $settings['price_dec_percent'] ?? 0.1;
		$initAmountPercent = $settings['init_amount_percent'] ?? 1.3;
		$amountIncPercent = $settings['amount_inc_percent'] ?? 5;
		$buyDelaySeconds = $settings['buy_delay_seconds'] ?? 360;
		$baseCurrency = new Currency($settings['baseCurrency'] ?? 'XRP');
		$quoteCurrency = new Currency($settings['quoteCurrency'] ?? 'BTC');
		$symbolString = $baseCurrency->getCode() . $quoteCurrency->getCode();

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
		$currentPrice = $askPrice;

		try {
			$firstBuyOrder = $this->orderRepository->findFirstBuy($session->getId());
		} catch (EntityNotFoundException $exception) {
			$firstBuyOrder = null;
		}

		try {
			$lastBuyOrder = $this->orderRepository->findLastBuy($session->getId());
		} catch (EntityNotFoundException $exception) {
			$lastBuyOrder = null;
		}

		try {
			$lastSellOrder = $this->orderRepository->findLastSell($session->getId());
		} catch (EntityNotFoundException $exception) {
			$lastSellOrder = null;
		}


		$minBalance = new Money(0, $baseCurrency);
		$amountInc = $this->moneyFromFloatPolicy->getMoney($baseCurrency, $exchange->getAmountIncrement($symbolString));
		$priceTickSize = $this->moneyFromFloatPolicy->getMoney($quoteCurrency, $exchange->getPriceTickSize($symbolString));
		$buyOrdersCount = 0;
		$sellOrdersCount = 0;
		$activeOrders = $this->orderRepository->findActive($session->getId());
		foreach ($activeOrders as $order) {
			if ($order->getType() === 'sell') {
				$sellOrdersCount++;
			} else {
				$buyOrdersCount++;
			}
		}


		if ($baseCurrencyBalances->getAvailableBalance()->greaterThan($minBalance)) {
			foreach ($activeOrders as $order) {
				if ($order->getType() === 'buy') {
					continue;
				}
				$cancelOrderRequest->setOrderId($order->getId());
				$this->cancelOrderUseCase->execute($cancelOrderRequest);
				$this->logger->info(sprintf('Session #%s: cancel sell order', (string)$session->getId()), [
					'orderId' => (string)$cancelOrderRequest->getOrderId(),
					'baseBalance' => $this->balancesAsArray($baseCurrencyBalances),
					'quoteBalance' => $this->balancesAsArray($quoteCurrencyBalances),
				]);
			}
			$balancesRequest->setCurrency($baseCurrency);
			$baseCurrencyBalances = $this->getBotTradingSessionBalancesUseCase->execute($balancesRequest);
			$balancesRequest->setCurrency($quoteCurrency);
			$quoteCurrencyBalances = $this->getBotTradingSessionBalancesUseCase->execute($balancesRequest);
			$this->logger->info(sprintf('Session #%s: update balance', (string)$session->getId()), [
				'baseBalance' => $this->balancesAsArray($baseCurrencyBalances),
				'quoteBalance' => $this->balancesAsArray($quoteCurrencyBalances),
			]);

			$sellAmount = $baseCurrencyBalances->getAvailableBalance();
			$quoteTotal = $quoteCurrencyBalances->getStartBalance()
				->subtract($quoteCurrencyBalances->getAccountBalance());
			$sellPrice = $this->round(
				$quoteTotal
					->multiply(1 + $profitPercent / 100)
					->divide($this->formatter->format($sellAmount)),
				$priceTickSize
			);
			if ($sellAmount->greaterThanOrEqual($amountInc)) {
				$createOrderRequest->setAmount($this->formatter->format($sellAmount));
				$createOrderRequest->setPrice($this->formatter->format($sellPrice));
				$createOrderRequest->setType('sell');
				try {
					$lastSellOrder = $this->createOrderUseCase->execute($createOrderRequest)->getOrder();
				} catch (InsufficientFundsException $exception) {
					$this->logger->warning(sprintf('Session #%s: sell order error Insufficient Funds Exception', (string)$session->getId()));
					return;
				}
				$this->logger->info(sprintf('Session #%s: sell order created', (string)$session->getId()), [
					'orderId' => (string)$lastSellOrder->getId(),
					'amount' => $sellAmount,
					'price' => $sellPrice,
					'baseBalance' => $this->balancesAsArray($baseCurrencyBalances),
					'quoteBalance' => $this->balancesAsArray($quoteCurrencyBalances),
				]);
			}
		} else {
			if (
				$sellOrdersCount === 0
				&& $firstBuyOrder !== null
				&& (
					$currentPrice > $firstBuyOrder->getPrice() * (1 + $priceDecPercent * 4 / 100)
					|| $lastSellOrder !== null
				)
			) {
				$activeOrders = $this->orderRepository->findActive($session->getId());
				foreach ($activeOrders as $order) {
					$cancelOrderRequest->setOrderId($order->getId());
					$this->cancelOrderUseCase->execute($cancelOrderRequest);
					$this->logger->info(sprintf('Session #%s: cancel order', (string)$session->getId()), [
						'orderId' => (string)$cancelOrderRequest->getOrderId(),
						'baseBalance' => $this->balancesAsArray($baseCurrencyBalances),
						'quoteBalance' => $this->balancesAsArray($quoteCurrencyBalances),
					]);
				}

				$balancesRequest->setCurrency($baseCurrency);
				$baseCurrencyBalances = $this->getBotTradingSessionBalancesUseCase->execute($balancesRequest);
				$balancesRequest->setCurrency($quoteCurrency);
				$quoteCurrencyBalances = $this->getBotTradingSessionBalancesUseCase->execute($balancesRequest);

				if ($baseCurrencyBalances->getAvailableBalance()->greaterThan($minBalance)) {
					return;
				}
				if ($lastSellOrder !== null) {
					$session->end();
					$this->logger->info(sprintf('Session #%s: end session', (string)$session->getId()), [
						'orderId' => (string)$lastSellOrder->getId(),
						'baseBalance' => $this->balancesAsArray($baseCurrencyBalances),
						'quoteBalance' => $this->balancesAsArray($quoteCurrencyBalances),
					]);
				}
				return;
			}
		}


		if ($lastBuyOrder && (time() - $lastBuyOrder->getUpdatedAt()->getTimestamp()) < $buyDelaySeconds) {
			return;
		}
		$filledBuyOrdersCount = $this->orderRepository->countFilledBuyOrders($session->getId());
		$buyNum = $filledBuyOrdersCount + 1;
		$price = $lastBuyOrder !== null ? $lastBuyOrder->getPrice() : ($currentPrice * (1 - $priceDecPercent / 100));
		$buyPrice = $price * (1 - $priceDecPercent / 100);
		$isPriceBelowBuyPrice = false;
		if ($buyPrice > $currentPrice) {
			$isPriceBelowBuyPrice = true;
			$buyPrice = $currentPrice;
		}
		$buyPrice = $this->formatter->format(
			$this->round($this->moneyFromFloatPolicy->getMoney($quoteCurrency, $buyPrice), $priceTickSize)
		);
		$initAmount = (new Money($quoteCurrencyBalances->getAvailableBalance()->getAmount(), $baseCurrency))
			->divide(100)
			->multiply($initAmountPercent)
			->divide($buyPrice);
		$amount = $firstBuyOrder !== null
			? $this->moneyFromFloatPolicy->getMoney($baseCurrency, $firstBuyOrder->getAmount())
			: $initAmount;
		$buyAmount = $this->round($amount, $amountInc)
			->multiply(1 + $amountIncPercent * $buyNum / 100);
		if ($buyAmount->lessThan($amountInc)) {
			$buyAmount = $amountInc;
		}
		$activeBuyOrder = null;
		foreach ($activeOrders as $order) {
			if ($order->getType() != 'buy') {
				continue;
			}
			if ($order->getAmount() != $this->formatter->format($buyAmount)) {
				$cancelOrderRequest->setOrderId($order->getId());
				$this->cancelOrderUseCase->execute($cancelOrderRequest);
				$this->logger->info(sprintf('Session #%s: cancel incorrect buy order', (string)$session->getId()), [
					'orderId' => (string)$cancelOrderRequest->getOrderId(),
					'baseBalance' => $this->balancesAsArray($baseCurrencyBalances),
					'quoteBalance' => $this->balancesAsArray($quoteCurrencyBalances),
				]);
			}
		}
		if ($buyOrdersCount < $buyOrderLimit) {
			$createOrderRequest->setType('buy');
			$createOrderRequest->setPrice($buyPrice);
			$createOrderRequest->setAmount($this->formatter->format($buyAmount));
			try {
				$lastBuyOrder = $this->createOrderUseCase->execute($createOrderRequest)->getOrder();
			} catch (InsufficientFundsException $exception) {
				$this->logger->warning(sprintf('Session #%s: buy order error Insufficient Funds Exception', (string)$session->getId()));
				return;
			}
			$this->logger->info(sprintf('Session #%s: buy order created', (string)$session->getId()), [
				'orderId' => (string)$lastBuyOrder->getId(),
				'buyNum' => $buyNum,
				'baseBalance' => $this->balancesAsArray($baseCurrencyBalances),
				'quoteBalance' => $this->balancesAsArray($quoteCurrencyBalances),
			]);
		}
	}


	/**
	 * @param Money $money
	 * @param Currency $counterCurrency
	 * @param int $roundingMode
	 *
	 * @return Money
	 */
	private function convert(Money $money, Currency $counterCurrency, $ratio, $roundingMode = Money::ROUND_DOWN)
	{

		$baseCurrency = $money->getCurrency();
		$baseCurrencySubunit = $this->currencies->subunitFor($baseCurrency);
		$counterCurrencySubunit = $this->currencies->subunitFor($counterCurrency);
		$subunitDifference = $baseCurrencySubunit - $counterCurrencySubunit;

		$ratio = (string)Number::fromString($ratio)->base10($subunitDifference);

		$counterValue = $money->multiply($ratio, $roundingMode);

		return new Money($counterValue->getAmount(), $counterCurrency);
	}

	private function round(Money $money, Money $rounding): Money
	{
		return new Money(floor($money->getAmount() / $rounding->getAmount()) * $rounding->getAmount(),
			$money->getCurrency());
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