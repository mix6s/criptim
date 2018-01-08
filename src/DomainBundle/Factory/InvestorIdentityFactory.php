<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/8/18
 * Time: 6:08 PM
 */

namespace DomainBundle\Factory;


use Doctrine\DBAL\Connection;
use Domain\Factory\InvestorIdentityFactoryInterface;
use Domain\ValueObject\InvestorIdentity;

class InvestorIdentityFactory implements InvestorIdentityFactoryInterface
{
	/**
	 * @var Connection
	 */
	private $connection;

	public function __construct(Connection $connection)
	{
		$this->connection = $connection;
	}

	/**
	 * @return InvestorIdentity
	 */
	public function getNextId(): InvestorIdentity
	{
		$value = $this->connection->query("select nextval('investor_id_seq')")->fetchColumn(0);
		return new InvestorIdentity((string)$value);
	}
}