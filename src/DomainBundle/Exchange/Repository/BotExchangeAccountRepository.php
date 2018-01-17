<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/17/18
 * Time: 4:48 PM
 */

namespace DomainBundle\Exchange\Repository;


use Doctrine\ORM\EntityRepository;
use Domain\Exception\EntityNotFoundException;
use Domain\Exchange\Entity\BotExchangeAccount;
use Domain\Exchange\Repository\BotExchangeAccountRepositoryInterface;
use Domain\Exchange\ValueObject\BotId;
use Domain\Exchange\ValueObject\ExchangeId;
use Money\Currency;

class BotExchangeAccountRepository extends EntityRepository implements BotExchangeAccountRepositoryInterface
{

	/**
	 * @param BotId $botId
	 * @param ExchangeId $exchangeId
	 * @param Currency $currency
	 * @return BotExchangeAccount
	 * @throws EntityNotFoundException
	 */
	public function findByBotIdExchangeIdCurrency(
		BotId $botId,
		ExchangeId $exchangeId,
		Currency $currency
	): BotExchangeAccount {
		/** @var BotExchangeAccount $account */
		$account = $this->find([
			'botId' => $botId,
			'exchangeId' => $exchangeId,
			'currency' => $currency
		]);
		if ($account === null) {
			throw new EntityNotFoundException('BotExchangeAccount not found');
		}
		return $account;
	}

	/**
	 * @param BotId $botId
	 * @param ExchangeId $exchangeId
	 * @return BotExchangeAccount[]
	 */
	public function findByBotIdExchangeId(
		BotId $botId,
		ExchangeId $exchangeId
	): array {
		return $this->findBy([
			'botId' => $botId,
			'exchangeId' => $exchangeId
		]);
	}

	public function save(BotExchangeAccount $account)
	{
		$this->getEntityManager()->persist($account);
		$this->getEntityManager()->flush($account);
	}
}