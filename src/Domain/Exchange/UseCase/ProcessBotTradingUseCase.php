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
use Domain\Exchange\Entity\BotExchangeAccount;
use Domain\Exchange\Entity\BotExchangeAccountTransaction;
use Domain\Exchange\Entity\BotTradingSession;
use Domain\Exchange\Entity\BotTradingSessionAccount;
use Domain\Exchange\Entity\BotTradingSessionAccountTransaction;
use Domain\Exchange\Entity\UserExchangeAccountTransaction;
use Domain\Exchange\Factory\IdFactoryInterface;
use Domain\Exchange\Repository\BotExchangeAccountRepositoryInterface;
use Domain\Exchange\Repository\BotExchangeAccountTransactionRepositoryInterface;
use Domain\Exchange\Repository\BotRepositoryInterface;
use Domain\Exchange\Repository\BotTradingSessionAccountRepositoryInterface;
use Domain\Exchange\Repository\BotTradingSessionAccountTransactionRepositoryInterface;
use Domain\Exchange\Repository\BotTradingSessionRepositoryInterface;
use Domain\Exchange\Repository\TradingStrategyRepositoryInterface;
use Domain\Exchange\Repository\UserExchangeAccountRepositoryInterface;
use Domain\Exchange\Repository\UserExchangeAccountTransactionRepositoryInterface;
use Domain\Exchange\UseCase\Request\ProcessBotTradingRequest;
use Money\Money;

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
	/**
	 * @var UserExchangeAccountRepositoryInterface
	 */
	private $userExchangeAccountRepository;
	/**
	 * @var UserExchangeAccountTransactionRepositoryInterface
	 */
	private $userExchangeAccountTransactionRepository;

	public function __construct(
		BotTradingSessionRepositoryInterface $botTradingSessionRepository,
		IdFactoryInterface $idFactory,
		TradingStrategyRepositoryInterface $tradingStrategyRepository,
		BotExchangeAccountRepositoryInterface $botExchangeAccountRepository,
		BotExchangeAccountTransactionRepositoryInterface $botExchangeAccountTransactionRepository,
		BotTradingSessionAccountRepositoryInterface $botTradingSessionAccountRepository,
		BotTradingSessionAccountTransactionRepositoryInterface $botTradingSessionAccountTransactionRepository,
		BotRepositoryInterface $botRepository,
	 	UserExchangeAccountRepositoryInterface $userExchangeAccountRepository,
		UserExchangeAccountTransactionRepositoryInterface $userExchangeAccountTransactionRepository
	) {
		$this->botTradingSessionRepository = $botTradingSessionRepository;
		$this->idFactory = $idFactory;
		$this->tradingStrategyRepository = $tradingStrategyRepository;
		$this->botExchangeAccountRepository = $botExchangeAccountRepository;
		$this->botTradingSessionAccountRepository = $botTradingSessionAccountRepository;
		$this->botRepository = $botRepository;
		$this->botExchangeAccountTransactionRepository = $botExchangeAccountTransactionRepository;
		$this->botTradingSessionAccountTransactionRepository = $botTradingSessionAccountTransactionRepository;
		$this->userExchangeAccountRepository = $userExchangeAccountRepository;
		$this->userExchangeAccountTransactionRepository = $userExchangeAccountTransactionRepository;
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
		$bot = $this->botRepository->findById($session->getBotId());
		$sessionAccounts = $this->botTradingSessionAccountRepository->findByBotTradingSessionId($session->getId());
		foreach ($sessionAccounts as $sessionAccount) {
			$transaction = $this->botTradingSessionAccountTransactionRepository->findLastBySessionIdCurrencyDate(
				$session->getId(),
				$sessionAccount->getCurrency(),
				$session->getCreatedAt()
			);
			$diff = $sessionAccount->getBalance()->subtract($transaction->getBalance());

			$outMoney = $sessionAccount->getBalance()->multiply(-1);
			$inMoney = $sessionAccount->getBalance();

			$sessionAccount->change($outMoney);
			$sessionAccountTransaction = new BotTradingSessionAccountTransaction(
				$this->idFactory->getBotTradingSessionAccountTransactionId(),
				$session->getId(),
				$outMoney->getCurrency(),
				$outMoney,
				$sessionAccount->getBalance(),
				BotTradingSessionAccountTransaction::TYPE_BOT_TRANSFER
			);
			$this->botTradingSessionAccountTransactionRepository->save($sessionAccountTransaction);
			$this->botTradingSessionAccountRepository->save($sessionAccount);

			try {
				$botAccount = $this->botExchangeAccountRepository->findByBotIdExchangeIdCurrency($bot->getId(), $bot->getExchangeId(), $sessionAccount->getCurrency());
			} catch (EntityNotFoundException $exception) {
				$botAccount = new BotExchangeAccount($bot->getId(), $bot->getExchangeId(), $inMoney->getCurrency());
			}
			$botAccount->change($inMoney);
			$botAccTransactionId = $this->idFactory->getBotExchangeAccountTransactionId();
			$botAccTransaction = new BotExchangeAccountTransaction(
				$botAccTransactionId,
				$bot->getId(),
				$bot->getExchangeId(),
				$inMoney->getCurrency(),
				$inMoney,
				$botAccount->getBalance(),
				BotExchangeAccountTransaction::TYPE_SESSION_TRANSFER
			);
			$this->botExchangeAccountTransactionRepository->save($botAccTransaction);
			$this->botExchangeAccountRepository->save($botAccount);
			if ($diff->isZero()) {
				continue;
			}
			$transactions = $this->userExchangeAccountTransactionRepository->findByExchangeIdCurrencyDate(
				$bot->getExchangeId(),
				$diff->getCurrency(),
				$session->getCreatedAt()
			);
			$sum = new Money(0, $diff->getCurrency());
			foreach ($transactions as $transaction) {
				$sum = $sum->add($transaction->getBalance());
			}

			foreach ($transactions as $transaction) {
				$multiplier = $transaction->getBalance()->divide($sum->getAmount(), Money::ROUND_DOWN);
				$userDiff = $diff->multiply($multiplier, Money::ROUND_DOWN);

				$userAccount = $this->userExchangeAccountRepository->findByUserIdExchangeIdCurrency(
					$transaction->getUserId(),
					$transaction->getExchangeId(),
					$userDiff->getCurrency()
				);
				$transactionId = $this->idFactory->getUserExchangeAccountTransactionId();
				$userAccount->change($userDiff);
				$userAccountTransaction = new UserExchangeAccountTransaction(
					$transactionId,
					$transaction->getUserId(),
					$transaction->getExchangeId(),
					$userDiff->getCurrency(),
					$userDiff,
					$userAccount->getBalance(),
					UserExchangeAccountTransaction::TYPE_TRADING_DIFF
				);
				$this->userExchangeAccountTransactionRepository->save($userAccountTransaction);
				$this->userExchangeAccountRepository->save($userAccount);
			}
		}
		$session->close();
		$this->botTradingSessionRepository->save($session);
	}

	private function processSession(BotTradingSession $session)
	{
		$tradingStrategy = $this->tradingStrategyRepository->findById($session->getTradingStrategyId());
		$tradingStrategy->processTrading($session);
		$session->process();
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
			$outMoney = $botAccount->getBalance()->multiply(-1);
			$inMoney = $botAccount->getBalance();


			$botAccount->change($outMoney);
			$botAccTransactionId = $this->idFactory->getBotExchangeAccountTransactionId();
			$botAccTransaction = new BotExchangeAccountTransaction(
				$botAccTransactionId,
				$bot->getId(),
				$bot->getExchangeId(),
				$outMoney->getCurrency(),
				$outMoney,
				$botAccount->getBalance(),
				BotExchangeAccountTransaction::TYPE_SESSION_TRANSFER
			);
			$this->botExchangeAccountTransactionRepository->save($botAccTransaction);
			$this->botExchangeAccountRepository->save($botAccount);

			try {
				$sessionAccount = $this->botTradingSessionAccountRepository->findByBotTradingSessionIdCurrency(
					$session->getId(),
					$inMoney->getCurrency()
				);
			} catch (EntityNotFoundException $exception) {
				$sessionAccount = new BotTradingSessionAccount($session->getId(), $inMoney->getCurrency());
			}
			$sessionAccount->change($inMoney->absolute());
			$sessionAccountTransaction = new BotTradingSessionAccountTransaction(
				$this->idFactory->getBotTradingSessionAccountTransactionId(),
				$session->getId(),
				$inMoney->getCurrency(),
				$inMoney->absolute(),
				$sessionAccount->getBalance(),
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