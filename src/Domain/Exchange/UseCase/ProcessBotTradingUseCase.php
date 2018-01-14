<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 13.01.2018
 * Time: 21:53
 */

namespace Domain\Exchange\UseCase;


use Domain\Exception\DomainException;
use Domain\Exception\EntityNotFoundException;
use Domain\Exchange\Entity\BotExchangeAccountTransaction;
use Domain\Exchange\Entity\BotTradingSession;
use Domain\Exchange\Entity\BotTradingSessionAccount;
use Domain\Exchange\Entity\BotTradingSessionAccountTransaction;
use Domain\Exchange\Factory\IdFactoryInterface;
use Domain\Exchange\Repository\BotExchangeAccountRepositoryInterface;
use Domain\Exchange\Repository\BotExchangeAccountTransactionRepositoryInterface;
use Domain\Exchange\Repository\BotRepositoryInterface;
use Domain\Exchange\Repository\BotTradingSessionAccountRepositoryInterface;
use Domain\Exchange\Repository\BotTradingSessionAccountTransactionRepositoryInterface;
use Domain\Exchange\Repository\BotTradingSessionRepositoryInterface;
use Domain\Exchange\Repository\TradingStrategyRepositoryInterface;
use Domain\Exchange\UseCase\Request\ProcessBotTradingRequest;

class ProcessBotTradingUseCase
{
	/**
	 * @var BotTradingSessionRepositoryInterface
	 */
	private $botTradingSessionRepository;
	/**
	 * @var IdFactoryInterface
	 */
	private $idFactory;
	/**
	 * @var TradingStrategyRepositoryInterface
	 */
	private $tradingStrategyRepository;
	/**
	 * @var BotExchangeAccountRepositoryInterface
	 */
	private $botExchangeAccountRepository;
	/**
	 * @var BotTradingSessionAccountRepositoryInterface
	 */
	private $botTradingSessionAccountRepository;
	/**
	 * @var BotRepositoryInterface
	 */
	private $botRepository;
	/**
	 * @var BotExchangeAccountTransactionRepositoryInterface
	 */
	private $botExchangeAccountTransactionRepository;
	/**
	 * @var BotTradingSessionAccountTransactionRepositoryInterface
	 */
	private $botTradingSessionAccountTransactionRepository;

	public function __construct(
		BotTradingSessionRepositoryInterface $botTradingSessionRepository,
		IdFactoryInterface $idFactory,
		TradingStrategyRepositoryInterface $tradingStrategyRepository,
		BotExchangeAccountRepositoryInterface $botExchangeAccountRepository,
		BotExchangeAccountTransactionRepositoryInterface $botExchangeAccountTransactionRepository,
		BotTradingSessionAccountRepositoryInterface $botTradingSessionAccountRepository,
		BotTradingSessionAccountTransactionRepositoryInterface $botTradingSessionAccountTransactionRepository,
		BotRepositoryInterface $botRepository
	) {
		$this->botTradingSessionRepository = $botTradingSessionRepository;
		$this->idFactory = $idFactory;
		$this->tradingStrategyRepository = $tradingStrategyRepository;
		$this->botExchangeAccountRepository = $botExchangeAccountRepository;
		$this->botTradingSessionAccountRepository = $botTradingSessionAccountRepository;
		$this->botRepository = $botRepository;
		$this->botExchangeAccountTransactionRepository = $botExchangeAccountTransactionRepository;
		$this->botTradingSessionAccountTransactionRepository = $botTradingSessionAccountTransactionRepository;
	}

	public function execute(ProcessBotTradingRequest $request)
	{
		$botId = $request->getBotId();
		try {
			$session = $this->botTradingSessionRepository->findLastByBotId($botId);
		} catch (EntityNotFoundException $exception) {
			if (!$this->isNeedToCreateSession($request)) {
				return;
			}
			$session = $this->createSession($request);
		}

		if ($session->getStatus() === BotTradingSession::STATUS_ENDED) {
			$this->closeSession($session);
		}
		if ($session->getStatus() === BotTradingSession::STATUS_CLOSED) {
			if (!$this->isNeedToCreateSession($request)) {
				return;
			}
			$session = $this->createSession($request);
		}
		if ($session->getStatus() === BotTradingSession::STATUS_ACTIVE) {
			$this->processSession($session);
		}
	}

	private function closeSession(BotTradingSession $session)
	{
		$session->close();
		$this->botTradingSessionRepository->save($session);
	}

	private function processSession(BotTradingSession $session)
	{
		$tradingStrategy = $this->tradingStrategyRepository->findById($session->getTradingStrategyId());
		$tradingStrategy->processTrading($session);
		$this->botTradingSessionRepository->save($session);
	}

	private function createSession(ProcessBotTradingRequest $request): BotTradingSession
	{
		$id = $this->idFactory->getBotTradingSessionId();
		$bot = $this->botRepository->findById($request->getBotId());
		$session = new BotTradingSession($id, $request->getBotId(), $bot->getTradingStrategyId(),
			$bot->getTradingStrategySettings());
		$botAccounts = $this->botExchangeAccountRepository->findByBotIdExchangeId($bot->getId(), $bot->getExchangeId());
		foreach ($botAccounts as $botAccount) {
			if ($botAccount->getBalance()->isZero()) {
				continue;
			}
			$transferMoney = $botAccount->getBalance()->absolute()->multiply(-1);
			$botAccount->change($transferMoney);
			$botAccTransactionId = $this->idFactory->getBotExchangeAccountTransactionId();
			$botAccTransaction = new BotExchangeAccountTransaction(
				$botAccTransactionId,
				$bot->getId(),
				$bot->getExchangeId(),
				$transferMoney->getCurrency(),
				$transferMoney,
				$botAccount->getBalance(),
				BotExchangeAccountTransaction::TYPE_SESSION_TRANSFER
			);
			$this->botExchangeAccountTransactionRepository->save($botAccTransaction);
			$this->botExchangeAccountRepository->save($botAccount);

			try {
				$sessionAccount = $this->botTradingSessionAccountRepository->findByBotTradingSessionIdCurrency(
					$session->getId(),
					$transferMoney->getCurrency()
				);
			} catch (EntityNotFoundException $exception) {
				$sessionAccount = new BotTradingSessionAccount($session->getId(), $transferMoney->getCurrency());
			}
			$sessionAccount->change($transferMoney->absolute());
			$sessionAccountTransaction = new BotTradingSessionAccountTransaction(
				$this->idFactory->getBotTradingSessionAccountTransactionId(),
				$transferMoney->getCurrency(),
				$transferMoney->absolute(),
				$botAccount->getBalance(),
				BotTradingSessionAccountTransaction::TYPE_BOT_TRANSFER
			);
			$this->botTradingSessionAccountTransactionRepository->save($sessionAccountTransaction);
			$this->botTradingSessionAccountRepository->save($sessionAccount);
		}
		$this->botTradingSessionRepository->save($session);
		return $session;
	}

	private function isNeedToCreateSession(ProcessBotTradingRequest $request): bool
	{
		$bot = $this->botRepository->findById($request->getBotId());
		$tradingStrategy = $this->tradingStrategyRepository->findById($bot->getTradingStrategyId());
		return $tradingStrategy->isNeedToStartTrading();
	}
}