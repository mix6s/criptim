<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/25/18
 * Time: 11:08 AM
 */

namespace Domain\Exchange\UseCase;


use Domain\Exchange\Entity\Bot;
use Domain\Exchange\Factory\IdFactoryInterface;
use Domain\Exchange\Repository\BotRepositoryInterface;
use Domain\Exchange\Repository\ExchangeRepositoryInterface;
use Domain\Exchange\Repository\TradingStrategyRepositoryInterface;
use Domain\Exchange\UseCase\Request\CreateBotRequest;
use Domain\Exchange\UseCase\Response\CreateBotResponse;

class CreateBotUseCase
{
	/**
	 * @var IdFactoryInterface
	 */
	private $idFactory;
	/**
	 * @var ExchangeRepositoryInterface
	 */
	private $exchangeRepository;
	/**
	 * @var TradingStrategyRepositoryInterface
	 */
	private $tradingStrategyRepository;
	/**
	 * @var BotRepositoryInterface
	 */
	private $botRepository;

	public function __construct(
		IdFactoryInterface $idFactory,
		ExchangeRepositoryInterface $exchangeRepository,
		TradingStrategyRepositoryInterface $tradingStrategyRepository,
		BotRepositoryInterface $botRepository
	) {
		$this->idFactory = $idFactory;
		$this->exchangeRepository = $exchangeRepository;
		$this->tradingStrategyRepository = $tradingStrategyRepository;
		$this->botRepository = $botRepository;
	}

	public function execute(CreateBotRequest $request): CreateBotResponse
	{
		$botId = $this->idFactory->getBotId();
		$exchange = $this->exchangeRepository->findById($request->getExchangeId());
		$tradingStrategy = $this->tradingStrategyRepository->findById($request->getTradingStrategyId());
		$bot = new Bot($botId, $exchange->getId(), $tradingStrategy->getId(), $request->getTradingStrategySettings());
		$this->botRepository->save($bot);
		return new CreateBotResponse($bot);
	}
}