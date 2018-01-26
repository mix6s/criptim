<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/17/18
 * Time: 5:25 PM
 */

namespace DomainBundle\Exchange\Repository;


use Doctrine\ORM\EntityRepository;
use Domain\Exception\EntityNotFoundException;
use Domain\Exchange\Entity\BotTradingSessionAccountTransaction;
use Domain\Exchange\Repository\BotTradingSessionAccountTransactionRepositoryInterface;
use Domain\Exchange\ValueObject\BotTradingSessionId;
use Money\Currency;

class BotTradingSessionAccountTransactionRepository extends EntityRepository implements BotTradingSessionAccountTransactionRepositoryInterface
{

	/**
	 * @param BotTradingSessionAccountTransaction $transaction
	 */
	public function save(BotTradingSessionAccountTransaction $transaction)
	{
		$this->getEntityManager()->persist($transaction);
		$this->getEntityManager()->flush($transaction);
	}

	/**
	 * @param BotTradingSessionId $sessionId
	 * @param Currency $currency
	 * @param \DateTimeImmutable $dt
	 * @return BotTradingSessionAccountTransaction
	 * @throws EntityNotFoundException
	 */
	public function findLastBySessionIdCurrencyDate(
		BotTradingSessionId $sessionId,
		Currency $currency,
		\DateTimeImmutable $dt
	): BotTradingSessionAccountTransaction {
		$transaction = $this->getEntityManager()->createQueryBuilder()
			->select('t')
			->from('Domain\Exchange\Entity\BotTradingSessionAccountTransaction', 't')
			->where('t.botTradingSessionId = :session_id')
			->andWhere('t.currency = :currency')
			->andWhere('t.dt <= :dt')
			->setParameter('session_id', $sessionId)
			->setParameter('currency', $currency)
			->setParameter('dt', $dt)
			->orderBy('t.dt', 'DESC')
			->getQuery()
			->getOneOrNullResult();
		if ($transaction === null) {
			throw new EntityNotFoundException('Latest BotTradingSessionAccountTransaction not found');
		}
		return $transaction;
	}
}