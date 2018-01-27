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
use Domain\Exchange\Entity\Order;
use Domain\Exchange\ValueObject\ExchangeId;
use Domain\Exchange\ValueObject\OrderId;
use GuzzleHttp\Client;

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

	public function __construct(string $id, string $publicKey, string $privateKey)
	{
		$this->id = new ExchangeId(self::ID . $id);
		$this->publicKey = $publicKey;
		$this->privateKey = $privateKey;
		$this->client = new Client();
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
		$orderId = (string)$order->getId();
		$len = strlen($orderId);
		if ($len < 8) {
			for ($i = $len; $i < 8; $i++) {
				$orderId = '0' . $orderId;
			}
		}
		$data = $this->apiAuthRequest('POST', '/order', [
			'clientOrderId' => $orderId,
			'symbol' => $order->getSymbol()->getBaseCurrency() . $order->getSymbol()->getCounterCurrency(),
			'side' => (string)$order->getType(),
			'quantity' => $order->getAmount(),
			'price' => $order->getPrice(),
		]);
	}

	public function cancelOrder(Order $order)
	{
		// TODO: Implement cancelOrder() method.
	}

	public function getSymbol(string $symbol)
	{
		// TODO: Implement getSymbol() method.
	}

	public function getFee()
	{
		return 0.1 / 100;
	}

	private function apiAuthRequest(string $method, string $uri, array $data = null)
	{
		$options = [
			'auth' => [$this->publicKey, $this->privateKey]
		];
		$method = strtoupper($method);
		if ($method === 'POST') {
			$options['form_params'] = $data;
		}
		$response = $this->client->request($method, self::API_ENDPOINT . $uri, $options);

		if ($response->getStatusCode() !== 200) {
			throw new DomainException(sprintf('HitBtc api response error: %s', $response->getReasonPhrase()), $response->getStatusCode());
		}

		$body = json_decode($response->getBody(), true);
		if (!empty($body['error'])) {
			throw new DomainException($body['error']['message'] ?? 'HitBtc api error', $body['error']['code'] ?? null);
		}
		return $body;
	}

	/**
	 * @return Order[]
	 */
	public function getActiveOrders(): array
	{
		return [];
	}

	/**
	 * @param OrderId $orderId
	 * @return Order
	 * @throws EntityNotFoundException
	 */
	public function getOrder(OrderId $orderId): Order
	{
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
		$data = $this->apiAuthRequest('GET', sprintf('/public/orderbook/%s', $symbol));
		return $data;
	}
}