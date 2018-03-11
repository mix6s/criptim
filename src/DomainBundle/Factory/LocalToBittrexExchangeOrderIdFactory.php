<?php


namespace DomainBundle\Factory;

use Doctrine\DBAL\Connection;
use Domain\Exchange\ValueObject\LocalToBittrexExchangeOrderId;
use Domain\Factory\LocalToBittrexExchangeOrderIdFactoryInterface;

class LocalToBittrexExchangeOrderIdFactory implements LocalToBittrexExchangeOrderIdFactoryInterface
{
	/**
	 * @var Connection
	 */
	private $connection;

	public function __construct(Connection $connection)
	{
		$this->connection = $connection;
	}

	public function getLocalToBittrexExchangeOrderId(): LocalToBittrexExchangeOrderId
	{
		$value = $this->connection->query("select nextval('local_to_bittrex_exchange_order_id_id_seq')")->fetchColumn(0);
		return new LocalToBittrexExchangeOrderId((string)$value);
	}
}