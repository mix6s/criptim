<?php
/**
 * Created by PhpStorm.
 * User: Simple
 * Date: 13.01.2018
 * Time: 21:53
 */

namespace Domain\Exchange\UseCase;


use Domain\Entity\UserAccountTransaction;
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
use Domain\Exchange\UseCase\Request\GetBotExchangeAccountRequest;
use Domain\Exchange\UseCase\Request\ProcessBotTradingRequest;
use Domain\Repository\UserAccountRepositoryInterface;
use Domain\Repository\UserAccountTransactionRepositoryInterface;
use DomainBundle\Exchange\Policy\CryptoMoneyFormatter;
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
	 * @var GetBotExchangeAccountUseCase
	 */
	private $getBotExchangeAccountUserCase;
	private $formatter;
	/**
	 * @var UserAccountRepositoryInterface
	 */
	private $userAccountRepository;
	/**
	 * @var UserAccountTransactionRepositoryInterface
	 */
	private $userAccountTransactionRepository;

	public function __construct(
		BotTradingSessionRepositoryInterface $botTradingSessionRepository,
		IdFactoryInterface $idFactory,
		TradingStrategyRepositoryInterface $tradingStrategyRepository,
		BotExchangeAccountRepositoryInterface $botExchangeAccountRepository,
		BotExchangeAccountTransactionRepositoryInterface $botExchangeAccountTransactionRepository,
		BotTradingSessionAccountRepositoryInterface $botTradingSessionAccountRepository,
		BotTradingSessionAccountTransactionRepositoryInterface $botTradingSessionAccountTransactionRepository,
		BotRepositoryInterface $botRepository,
	 	UserAccountRepositoryInterface $userAccountRepository,
		UserAccountTransactionRepositoryInterface $userAccountTransactionRepository,
		GetBotExchangeAccountUseCase $getBotExchangeAccountUserCase
	) {
		$this->botTradingSessionRepository = $botTradingSessionRepository;
		$this->idFactory = $idFactory;
		$this->tradingStrategyRepository = $tradingStrategyRepository;
		$this->botExchangeAccountRepository = $botExchangeAccountRepository;
		$this->botTradingSessionAccountRepository = $botTradingSessionAccountRepository;
		$this->botRepository = $botRepository;
		$this->botExchangeAccountTransactionRepository = $botExchangeAccountTransactionRepository;
		$this->botTradingSessionAccountTransactionRepository = $botTradingSessionAccountTransactionRepository;
		$this->getBotExchangeAccountUserCase = $getBotExchangeAccountUserCase;
		$this->formatter = new CryptoMoneyFormatter();
		$this->userAccountRepository = $userAccountRepository;
		$this->userAccountTransactionRepository = $userAccountTransactionRepository;
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
			return;
		}

		if ($session->getStatus() === BotTradingSession::STATUS_ENDED) {
			$this->closeSession($session);
			return;
		}
		if ($session->getStatus() === BotTradingSession::STATUS_CLOSED) {
			if (!$this->isNeedToCreateSession($request)) {
				return;
			}
			$session = $this->createSession($request);
			return;
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
			try {
				$transaction = $this->botTradingSessionAccountTransactionRepository->findLastBySessionIdCurrencyDate(
					$session->getId(),
					$sessionAccount->getCurrency(),
					$session->getCreatedAt()
				);
			} catch (EntityNotFoundException $exception) {
				continue;
			}
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

			$getBotExchangeAccountRequest = new GetBotExchangeAccountRequest();
			$getBotExchangeAccountRequest->setBotId($bot->getId());
			$getBotExchangeAccountRequest->setExchangeId($bot->getExchangeId());
			$getBotExchangeAccountRequest->setCurrency($sessionAccount->getCurrency());
			$botAccount = $this->getBotExchangeAccountUserCase->execute($getBotExchangeAccountRequest)->getBotExchangeAccount();
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
			$transactions = $this->userAccountTransactionRepository->findLastByCurrencyAndDate(
				$diff->getCurrency(),
				$session->getCreatedAt()
			);
			$sum = new Money(0, $diff->getCurrency());
			foreach ($transactions as $transaction) {
				$sum = $sum->add($transaction->getBalance());
			}

			foreach ($transactions as $transaction) {
				$sumAmount = $this->formatter->format($sum);
				$multiplier = $transaction->getBalance()->divide($sumAmount, Money::ROUND_DOWN);
				$multiplierAmount = $this->formatter->format($multiplier);
				$userDiff = $diff->multiply($multiplierAmount, Money::ROUND_DOWN);
				$userAccount = $this->userAccountRepository->findByUserIdCurrency(
					$transaction->getUserId(),
					$userDiff->getCurrency()
				);
				$transactionId = $this->idFactory->getUserAccountTransactionId();
				$userAccount->change($userDiff);
				$userAccountTransaction = new UserAccountTransaction(
					$transactionId,
					$transaction->getUserId(),
					$userDiff->getCurrency(),
					$userDiff,
					$userAccount->getBalance(),
					UserAccountTransaction::TYPE_TRADING_DIFF
				);
				$this->userAccountTransactionRepository->save($userAccountTransaction);
				$this->userAccountRepository->save($userAccount);
			}
		}
		$session->close();
		$this->botTradingSessionRepository->save($session);
	}

	private function processSession(BotTradingSession $session)
	{
		$tradingStrategy = $this->tradingStrategyRepository->findById($session->getTradingStrategyId());
		$session->process();
		$tradingStrategy->processTrading($session);
		$this->botTradingSessionRepository->save($session);
	}

	private function createSession(ProcessBotTradingRequest $request): BotTradingSession
	{
		$sessionId = $this->idFactory->getBotTradingSessionId();
		$bot = $this->botRepository->findById($request->getBotId());

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
					$sessionId,
					$inMoney->getCurrency()
				);
			} catch (EntityNotFoundException $exception) {
				$sessionAccount = new BotTradingSessionAccount($sessionId, $inMoney->getCurrency());
			}
			$sessionAccount->change($inMoney->absolute());
			$sessionAccountTransaction = new BotTradingSessionAccountTransaction(
				$this->idFactory->getBotTradingSessionAccountTransactionId(),
				$sessionId,
				$inMoney->getCurrency(),
				$inMoney->absolute(),
				$sessionAccount->getBalance(),
				BotTradingSessionAccountTransaction::TYPE_BOT_TRANSFER
			);
			$this->botTradingSessionAccountTransactionRepository->save($sessionAccountTransaction);
			$this->botTradingSessionAccountRepository->save($sessionAccount);
		}
		$session = new BotTradingSession($sessionId, $request->getBotId(), $bot->getExchangeId(), $bot->getTradingStrategyId(), $bot->getTradingStrategySettings());
		$this->botTradingSessionRepository->save($session);
		return $session;
	}

	private function isNeedToCreateSession(ProcessBotTradingRequest $request): bool
	{
		$bot = $this->botRepository->findById($request->getBotId());
		if (!$bot->isActive()) {
			return false;
		}
		$tradingStrategy = $this->tradingStrategyRepository->findById($bot->getTradingStrategyId());
		return $tradingStrategy->isNeedToStartTrading($bot);
	}
}