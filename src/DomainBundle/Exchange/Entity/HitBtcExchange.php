<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/19/18
 * Time: 11:50 AM
 */

namespace DomainBundle\Exchange\Entity;


use Domain\Exception\DomainException;
use Domain\Exchange\Entity\ExchangeInterface;
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

	public function createOrder(OrderId $orderId)
	{
		// TODO: Implement createOrder() method.
	}

	public function cancelOrder(OrderId $orderId)
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
		$response = $this->client->request($method, $uri, $options);

		if ($response->getStatusCode() !== 200) {
			throw new DomainException(sprintf('HitBtc api response error: %s', $response->getReasonPhrase()), $response->getStatusCode());
		}

		$body = json_decode($response->getBody());
		if (!empty($body['error'])) {
			throw new DomainException($body['error']['message'] ?? 'HitBtc api error', $body['error']['code'] ?? null);
		}

	}
}