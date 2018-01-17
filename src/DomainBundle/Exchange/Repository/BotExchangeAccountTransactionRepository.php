<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/17/18
 * Time: 4:56 PM
 */

namespace DomainBundle\Exchange\Repository;


use Doctrine\ORM\EntityRepository;
use Domain\Exchange\Entity\BotExchangeAccountTransaction;
use Domain\Exchange\Repository\BotExchangeAccountTransactionRepositoryInterface;

class BotExchangeAccountTransactionRepository extends EntityRepository implements BotExchangeAccountTransactionRepositoryInterface
{

	public function save(BotExchangeAccountTransaction $transaction)
	{
		$this->getEntityManager()->persist($transaction);
		$this->getEntityManager()->flush($transaction);
	}
}