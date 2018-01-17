<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/17/18
 * Time: 5:22 PM
 */

namespace DomainBundle\Exchange\Repository;


use Doctrine\ORM\EntityRepository;
use Domain\Exception\EntityNotFoundException;
use Domain\Exchange\Entity\BotTradingSessionAccount;
use Domain\Exchange\Repository\BotTradingSessionAccountRepositoryInterface;
use Domain\Exchange\ValueObject\BotTradingSessionId;
use Money\Currency;

class BotTradingSessionAccountRepository extends EntityRepository implements BotTradingSessionAccountRepositoryInterface
{

	/**
	 * @param BotTradingSessionId $botTradingSessionId
	 * @param Currency $currency
	 * @return BotTradingSessionAccount
	 * @throws EntityNotFoundException
	 */
	public function findByBotTradingSessionIdCurrency(
		BotTradingSessionId $botTradingSessionId,
		Currency $currency
	): BotTradingSessionAccount {
		/** @var BotTradingSessionAccount $account */
		$account = $this->find([
			'botTradingSessionId' => $botTradingSessionId,
			'currency' => $currency
		]);
		if ($account === null) {
			throw new EntityNotFoundException('BotTradingSessionAccount not found');
		}
		return $account;
	}

	/**
	 * @param BotTradingSessionId $botTradingSessionId
	 * @return BotTradingSessionAccount[]
	 */
	public function findByBotTradingSessionId(BotTradingSessionId $botTradingSessionId): array
	{
		return $this->findBy([
			'botTradingSessionId' => $botTradingSessionId,
		]);
	}

	/**
	 * @param BotTradingSessionAccount $account
	 */
	public function save(BotTradingSessionAccount $account)
	{
		$this->getEntityManager()->persist($account);
		$this->getEntityManager()->flush($account);
	}
}