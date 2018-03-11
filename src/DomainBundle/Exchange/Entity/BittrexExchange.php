<?php


namespace DomainBundle\Exchange\Entity;


use Domain\Exchange\Entity\ExchangeInterface;
use Domain\Exchange\Entity\ExchangeOrder;
use Domain\Exchange\Entity\Order;
use Domain\Exchange\Entity\LocalToBittrexExchangeOrder;
use Domain\Exchange\Factory\IdFactoryInterface;
use Domain\Exchange\Repository\LocalToBittrexExchangeOrderRepositoryInterface;
use Domain\Exchange\ValueObject\BittrexOrderId;
use Domain\Exchange\ValueObject\Candle;
use Domain\Exchange\ValueObject\ExchangeId;
use Domain\Exchange\ValueObject\OrderId;
use DomainBundle\Exception\BittrexApiException;
use GuzzleHttp\ClientInterface;
use Money\Currency;
use Psr\Log\LoggerInterface;

class BittrexExchange implements ExchangeInterface
{
	const ID = 'bittrex';

	const API_ENDPOINT = 'https://bittrex.com/api/v1.1';

	const API_VERSION_11 = 'VERSION_11';
	const API_VERSION_20 = 'VERSION_20';

	private static $apiEndpointForApiVersion = [
		self::API_VERSION_11 => 'https://bittrex.com/api/v1.1',
		self::API_VERSION_20 => 'https://bittrex.com/api/v2.0'
	];

	private static $orderTypesMap = [
		'LIMIT_BUY' => 'buy',
		'LIMIT_SELL' => 'sell'
	];

	/**
	 * @var string
	 */
	private $id;
	/**
	 * @var string
	 */
	private $publicKey;
	/**
	 * @var string
	 */
	private $privateKey;
	/**
	 * @var LocalToBittrexExchangeOrderRepositoryInterface
	 */
	private $localToBittrexExchangeOrderRepository;
	/**
	 * @var IdFactoryInterface
	 */
	private $idFactory;
	/**
	 * @var LoggerInterface
	 */
	private $logger;
	/**
	 * @var ClientInterface
	 */
	private $client;

	public function __construct(
		string $id,
		string $publicKey,
		string $privateKey,
		LoggerInterface $logger,
		ClientInterface $client,
		LocalToBittrexExchangeOrderRepositoryInterface $bittrexToLocalExchangeOrderRepository,
		IdFactoryInterface $idFactory
	)
	{
		$this->id = new ExchangeId(self::ID . $id);
		$this->publicKey = $publicKey;
		$this->privateKey = $privateKey;
		$this->localToBittrexExchangeOrderRepository = $bittrexToLocalExchangeOrderRepository;
		$this->idFactory = $idFactory;
		$this->logger = $logger;
		$this->client = $client;
	}

	/**
	 * @return ExchangeId
	 */
	public function getId(): ExchangeId
	{
		return $this->id;
	}

	public function getSymbolForCurrencies(Currency $base, Currency $quote): string
	{
		return $base . '-' . $quote;
	}

	public function createOrder(Order $order): void
	{
		$market = $this->getSymbolForCurrencies(
			$order->getSymbol()->getBaseCurrency(),
			$order->getSymbol()->getCounterCurrency()
		);
		$quantity = $order->getAmount();
		$rate = $order->getPrice();

		try {
			switch ($order->getType()) {
				case 'buy':
					/*https://bittrex.com/api/v1.1/market/buylimit?apikey=API_KEY&market=BTC-LTC&quantity=1.2&rate=1.3*/
					$uri = '/market/buylimit';
					break;
				case 'sell':
					/*https://bittrex.com/api/v1.1/market/selllimit?apikey=API_KEY&market=BTC-LTC&quantity=1.2&rate=1.3*/
					$uri = '/market/selllimit';
					break;
				default:
					throw new BittrexApiException('Unknown order type');
			}
			$response = $this->apiAuthRequest(
				self::API_VERSION_11,
				$uri,
				[
					'market' => $market,
					'quantity' => $quantity,
					'rate' => $rate,
				]
			);
			$localToBittrexExchangeOrder = new LocalToBittrexExchangeOrder(
				$this->idFactory->getLocalToBittrexExchangeOrderId(),
				$order->getId(),
				new BittrexOrderId($response['uuid'])
			);
			$this->localToBittrexExchangeOrderRepository->save($localToBittrexExchangeOrder);
		} catch (BittrexApiException $exception) {
			throw $exception;
		}
	}

	public function cancelOrder(OrderId $orderId): ExchangeOrder
	{
		/*https://bittrex.com/api/v1.1/market/cancel?apikey=API_KEY&uuid=ORDER_UUID*/
		$localToBittrexExchangeOrder = $this->localToBittrexExchangeOrderRepository->findByOrderId($orderId);
		try {
			$this->apiAuthRequest(
				self::API_VERSION_11,
				'/market/cancel',
				[
					'uuid' => $localToBittrexExchangeOrder->getBittrexOrderId(),
				]
			);
			return $this->getOrder($orderId);
		} catch (BittrexApiException $exception) {
			throw new BittrexApiException('Cancel request was not proceed');
		}
	}

	public function getFee(): float
	{
		/*https://bittrex.com/Fees*/
		return 0.25 / 100;
	}

	public function getBid(string $symbol): float
	{
		/*https://bittrex.com/api/v1.1/public/getticker*/
		$ticker = $this->apiNonAuthRequest(
			self::API_VERSION_11,
			'/public/getticker',
			[
				'market' => $symbol,
			]
		);
		$bid = $ticker['Bid'] ?? null;
		if ($bid) {
			return (float)$bid;
		}
		throw new BittrexApiException('Bid not found');
	}

	public function getAsk(string $symbol): float
	{
		$ticker = $this->apiNonAuthRequest(
			self::API_VERSION_11,
			'/public/getticker',
			[
				'market' => $symbol,
			]
		);
		$bid = $ticker['Ask'] ?? null;
		if ($bid) {
			return (float)$bid;
		}
		throw new BittrexApiException('Ask not found');
	}

	public function getPriceTickSize(string $symbol): float
	{
		return 0.000001; // maybe
	}

	public function getAmountIncrement(string $symbol): float
	{
		$markets = $this->apiNonAuthRequest(
			self::API_VERSION_11,
			'/public/markets'
		);

		[$baseCurrency, $marketCurrency] = explode('-', $symbol);

		foreach ($markets as $market) {
			if (
				$market['BaseCurrency'] === $baseCurrency
				&&
				$market['MarketCurrency'] === $marketCurrency
			) {
				return (float)$market['MinTradeSize'];
			}
		}
		throw new BittrexApiException('Symbol not found');
	}

	/**
	 * @param Currency $base
	 * @param Currency $quote
	 * @param \DateInterval $period
	 * @param int $count
	 * @return Candle[]
	 * @throws BittrexApiException
	 */
	public function getCandles(Currency $base, Currency $quote, \DateInterval $period, int $count): array
	{
		/*https://bittrex.com/Api/v2.0/pub/market/GetTicks?marketName=BTC-LTC&tickInterval=thirtyMin*/
		$marketName = $this->getSymbolForCurrencies($base, $quote);
		$tickInterval = $this->resolveTickIntervalFromInterval($period);
		$data = $this->apiNonAuthRequest(
			self::API_VERSION_20,
			'/pub/market/getTicks',
			[
				'marketName' => $marketName,
				'tickInterval' => $tickInterval,
				'_' => (new \DateTimeImmutable())->getTimestamp(),
			]
		);
		$candles = [];
		foreach ($data as $item) {
			/*
			 * {
O: 0.01619855,
H: 0.01619855,
L: 0.01606753,
C: 0.01606801,
V: 831.24825882,
T: "2018-01-30T12:30:00",
BV: 13.41926585
},
			*/
			$candles[] = new Candle(
				(float)$item['C'],
				new \DateTimeImmutable($item['T'])
			);
		}
		return $candles;
	}

	/**
	 * @return ExchangeOrder[]
	 * @throws BittrexApiException
	 */
	public function getActiveOrders(): array
	{
		/*https://bittrex.com/api/v1.1/market/getopenorders?apikey=API_KEY&market=BTC-LTC*/
		$rawOpenOrders = $this->apiAuthRequest(
			self::API_VERSION_11,
			'/market/getopenorders'
		);
		$orders = [];
		foreach ($rawOpenOrders as $rawOpenOrder) {
			$typeGetter = function ($data) {
				return self::$orderTypesMap[$data['OrderType']];
			};
			$orders[] = $this->toExchangeOrder($rawOpenOrder, $typeGetter);
		}
		return $orders;
	}

	public function getOrder(OrderId $orderId): ExchangeOrder
	{
		$localToBittrexExchangeOrder = $this->localToBittrexExchangeOrderRepository->findByOrderId($orderId);
		/*https://bittrex.com/api/v1.1/account/getorder&uuid=0cb4c4e4-bdc7-4e13-8c13-430e587d2cc1*/
		$order = $this->apiAuthRequest(
			self::API_VERSION_11,
			'/account/getorder',
			[
				'uuid' => $localToBittrexExchangeOrder->getBittrexOrderId()
			]
		);

		$typeGetter = function ($data) {
			return self::$orderTypesMap[$data['Type']];
		};
		return $this->toExchangeOrder($order, $typeGetter);
	}

	/**
	 * @param string $apiVersion
	 * @param string $uri
	 * @param array $queryParams
	 * @return array
	 * @throws BittrexApiException
	 */
	private function apiAuthRequest(string $apiVersion, string $uri, array $queryParams = []): array
	{
		$queryParams = array_merge($queryParams, [
			'apikey' => $this->publicKey,
			'nonce' => time(),
		]);

		$query = self::$apiEndpointForApiVersion[$apiVersion] . $uri . '?' . http_build_query($queryParams);

		$options = [
			'headers' => [
				'apisign' => hash_hmac('sha512', $query, $this->privateKey)
			]
		];

		try {
			return $this->clientRequest('GET', $query, $options);
		} catch (BittrexApiException $exception) {
			$this->logger->error('Bittrex client request error');
			throw $exception;
		}
	}

	/**
	 * @param string $apiVersion
	 * @param string $uri
	 * @param array $queryParams
	 * @return array
	 * @throws BittrexApiException
	 */
	private function apiNonAuthRequest(string $apiVersion, string $uri, array $queryParams = []): array
	{
		$query = self::$apiEndpointForApiVersion[$apiVersion] . $uri . '?' . http_build_query($queryParams);

		try {
			return $this->clientRequest('GET', $query);
		} catch (BittrexApiException $exception) {
			$this->logger->error('Bittrex client request error');
			throw $exception;
		}
	}

	private function toExchangeOrder(
		array $data,
		callable $typeFetcher
	): ExchangeOrder
	{
		$id = new OrderId($data['OrderUuid'] ?? null);
		$type = $typeFetcher($data);
		$price = (float)$data['Price'];
		$amount = (float)$data['Quantity'];
		$quantityRemaining = (float)$data['QuantityRemaining'];
		$execAmount = $amount - $quantityRemaining;
		$symbol = $data['Exchange'];

		$status = Order::STATUS_PARTIALLY_FILLED;
		if ($execAmount === 0) {
			$status = Order::STATUS_FILLED;
		}
		if ($data['CancelInitiated']) {
			$status = Order::STATUS_CANCELED;
		}

		return new ExchangeOrder(
			$id,
			$type,
			$price,
			$amount,
			$execAmount,
			$symbol,
			$status
		);
	}

	private function clientRequest(string $method, string $query, array $options = []): array
	{
		$requestContext = [
			'query' => $query,
			'options' => json_encode($options, true)
		];
		$this->logger->info('Bittrex request', $requestContext);
		$response = $this->client->request($method, $query, $options);
		$body = json_decode($response->getBody(), true);
		$this->logger->info('Bittrex response', $body);
		$isSucceed = $body['success'] ?? false;
		if ($isSucceed !== true) {
			throw new BittrexApiException('Bittrex response is not succeed');
		}
		return $body['result'];
	}

	private function resolveTickIntervalFromInterval(\DateInterval $period): string
	{
		$secondsPeriod = (new \DateTimeImmutable('@0'))->add($period)->getTimestamp();
		switch ($secondsPeriod) {
			case 60:
				return 'oneMin';
			case 60 * 5:
				return 'fiveMin';
			case 60 * 30:
				return 'thirtyMin';
			case 60 * 60:
				return 'hour';
			case 60 * 60 * 24:
				return 'day';
			default:
				throw new BittrexApiException('Unknown period');
		}
	}

}