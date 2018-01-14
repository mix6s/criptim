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
use Domain\Exchange\Entity\BotTradingSession;
use Domain\Exchange\Factory\IdFactoryInterface;
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

	public function __construct(
		BotTradingSessionRepositoryInterface $botTradingSessionRepository,
		IdFactoryInterface $idFactory,
		TradingStrategyRepositoryInterface $tradingStrategyRepository
	)
	{
		$this->botTradingSessionRepository = $botTradingSessionRepository;
		$this->idFactory = $idFactory;
		$this->tradingStrategyRepository = $tradingStrategyRepository;
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

	}

	private function processSession(BotTradingSession $session)
	{

	}

	private function createSession(ProcessBotTradingRequest $request): BotTradingSession
	{
		$id = $this->idFactory->getBotTradingSessionId();
		$session = new BotTradingSession($id, $request->getBotId(), $request->getTradingStrategyId(), $request->getTradingStrategySettings());

		return $session;
	}

	private function isNeedToCreateSession(ProcessBotTradingRequest $request): bool
	{
		$tradingStrategyId = $request->getTradingStrategyId();
		$tradingStrategy = $this->tradingStrategyRepository->findById($tradingStrategyId);
		return $tradingStrategy->isNeedToStartTrading();
	}
}