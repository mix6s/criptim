<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/8/18
 * Time: 6:08 PM
 */

namespace DomainBundle\Factory;


use Doctrine\DBAL\Connection;
use Domain\Factory\UserIdFactoryInterface;
use Domain\ValueObject\UserId;

class UserIdFactory implements UserIdFactoryInterface
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
	 * @return UserId
	 */
	public function getUserId(): UserId
	{
		$value = $this->connection->query("select nextval('user_id_seq')")->fetchColumn(0);
		return new UserId((string)$value);
	}
}