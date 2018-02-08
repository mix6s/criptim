<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/19/18
 * Time: 11:50 AM
 */

namespace DomainBundle\Exchange\Entity;


use Domain\Exception\DomainException;
use Domain\Exception\EntityNotFoundException;
use Domain\Exchange\Entity\ExchangeInterface;
use Domain\Exchange\Entity\ExchangeOrder;
use Domain\Exchange\Entity\Order;
use Domain\Exchange\ValueObject\ExchangeId;
use Domain\Exchange\ValueObject\OrderId;
use DomainBundle\Exception\HitBtcApiException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Psr\Log\LoggerInterface;

class HitBtcExchange implements ExchangeInterface
{
	const ID = 'hitbtc';

	const API_ENDPOINT = 'https://api.hitbtc.com/api/2';
	/**
	 * @var ExchangeId
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
	 * @var Client
	 */
	private $client;
	/**
	 * @var null|array
	 */
	private $symbolData;
	/**
	 * @var LoggerInterface
	 */
	private $logger;

	public function __construct(string $id, string $publicKey, string $privateKey, LoggerInterface $logger)
	{
		$this->id = new ExchangeId(self::ID . $id);
		$this->publicKey = $publicKey;
		$this->privateKey = $privateKey;
		$this->client = new Client();
		$this->symbolData = null;
		$this->logger = $logger;
	}

	/**
	 * @return ExchangeId
	 */
	public function getId(): ExchangeId
	{
		return $this->id;
	}

	public function createOrder(Order $order)
	{
		$data = [
			'clientOrderId' => $this->getOrderId($order->getId()),
			'symbol' => $order->getSymbol()->getBaseCurrency() . $order->getSymbol()->getCounterCurrency(),
			'side' => (string)$order->getType(),
			'quantity' => $order->getAmount(),
			'price' => $order->getPrice(),
		];
		try {
			$this->apiAuthRequest('POST', '/order', $data);
		} catch (HitBtcApiException $exception) {
			$this->logger->warning("HitBtc createOrder exception", [
				'code' => $exception->getCode(),
				'message' => $exception->getMessage(),
				'order_data' => $data
			]);
			if (!$exception->getCode() == '20008') {
				throw $exception;
			}
		}
	}

	private function getOrderId(OrderId $id)
	{
		$orderId = (string)$id;
		$len = strlen($orderId);
		if ($len < 8) {
			for ($i = $len; $i < 8; $i++) {
				$orderId = '0' . $orderId;
			}
		}
		return implode('_', [$this->getId(), $orderId]);
	}


	public function cancelOrder(OrderId $orderId): ExchangeOrder
	{
		$data = $this->apiAuthRequest('DELETE', sprintf('/order/%s', $this->getOrderId($orderId)));
		return $this->toExchangeOrder($data);
	}

	public function getSymbol(string $symbol)
	{
		// TODO: Implement getSymbol() method.
	}

	public function getFee()
	{
		return 0.1 / 100;
	}

	private function apiAuthRequest(string $method, string $uri, array $data = null, bool $logResponse = true)
	{
		$options = [
			'auth' => [$this->publicKey, $this->privateKey]
		];
		$method = strtoupper($method);
		if ($method === 'POST') {
			$options['form_params'] = $data;
		}
		$this->logger->debug("Api request", [
			'uri' => $uri,
			'method' => $method,
			'data' => $options,
		]);
		try {
			$response = $this->client->request($method, self::API_ENDPOINT . $uri, $options);
			$body = json_decode($response->getBody(), true);
			$this->logger->debug("Api response", [
				'uri' => $uri,
				'method' => $method,
				'data' => $options,
				'raw' => $logResponse ? $response->getBody() : '',
				'response' => $logResponse ? $body : ''
			]);
			return $body;
		} catch (ClientException $exception) {
			$body = json_decode($exception->getResponse()->getBody(), true);
			$this->logger->debug("Api error response", [
				'uri' => $uri,
				'method' => $method,
				'data' => $options,
				'raw' => $logResponse ? $exception->getResponse()->getBody() : '',
				'response' => $logResponse ? $body : ''
			]);
			throw new HitBtcApiException($body['error']['message'] ?? 'HitBtc api error', $body['error']['code'] ?? null);
		}
	}

	/**
	 * @return ExchangeOrder[]
	 */
	public function getActiveOrders(): array
	{
		$data = $this->apiAuthRequest('GET', '/order');
		$this->logger->debug("Active Orders", $data);
		$orders = [];
		foreach ($data as $item) {
			$orders[] = $this->toExchangeOrder($item);
		}
		return $orders;
	}

	/**
	 * @param array $data
	 * @return ExchangeOrder
	 */
	private function toExchangeOrder(array $data): ExchangeOrder
	{
		$parts = explode('_', $data['clientOrderId']);
		$orderId = (int)($parts[count($parts) - 1] ?? $parts[0]); //old order ids support
		return new ExchangeOrder(
			new OrderId((string)$orderId),
			$data['side'] ?? null,
			$data['price'] ?? null,
			$data['quantity'] ?? null,
			$data['cumQuantity'] ?? null,
			$data['symbol'] ?? null,
			$data['status'] ?? null
		);
	}

	/**
	 * @param OrderId $orderId
	 * @return ExchangeOrder
	 * @throws EntityNotFoundException
	 */
	public function getOrder(OrderId $orderId): ExchangeOrder
	{
		$data = $this->apiAuthRequest('GET', sprintf('/history/order?clientOrderId=%s', $this->getOrderId($orderId)));
		if (!empty($data[0])) {
			return $this->toExchangeOrder($data[0]);
		}
		throw new EntityNotFoundException();
	}

	public function getBid(string $symbol): float
	{
		$orderbook = $this->getOrderBook($symbol);
		return $orderbook['bid'][0]['price'];
	}

	public function getAsk(string $symbol): float
	{
		$orderbook = $this->getOrderBook($symbol);
		return $orderbook['ask'][0]['price'];
	}

	private function getOrderBook(string $symbol)
	{
		$data = $this->apiAuthRequest('GET', sprintf('/public/orderbook/%s', $symbol), null, false);
		return $data;
	}

	public function getPriceTickSize(string $symbol): float
	{
		$data = $this->getSymbolData();
		foreach ($data as $symbolData) {
			if ($symbolData['id'] === $symbol) {
				return $symbolData['tickSize'];
			}
		}
		throw new DomainException('Symbol not found');
	}

	public function getAmountIncrement(string $symbol): float
	{
		$data = $this->getSymbolData();
		foreach ($data as $symbolData) {
			if ($symbolData['id'] === $symbol) {
				return $symbolData['quantityIncrement'];
			}
		}
		throw new DomainException('Symbol not found');
	}

	private function getSymbolData()
	{
		if ($this->symbolData === null) {
			$this->symbolData = $this->apiAuthRequest('GET', '/public/symbol', null, false);
		}
		return $this->symbolData;
	}
}