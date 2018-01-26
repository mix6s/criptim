<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/25/18
 * Time: 11:08 AM
 */

namespace Domain\Exchange\UseCase;

use Domain\Exchange\Factory\IdFactoryInterface;
use Domain\Exchange\Repository\BotRepositoryInterface;
use Domain\Exchange\Repository\ExchangeRepositoryInterface;
use Domain\Exchange\Repository\TradingStrategyRepositoryInterface;
use Domain\Exchange\UseCase\Request\EditBotRequest;
use Domain\Exchange\UseCase\Response\EditBotResponse;

class EditBotUseCase
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

	public function execute(EditBotRequest $request): EditBotResponse
	{
		$bot = $this->botRepository->findById($request->getBotId());

		if ($request->getExchangeId() !== null) {
			$exchange = $this->exchangeRepository->findById($request->getExchangeId());
			$bot->changeExchangeId($exchange->getId());
		}

		if ($request->getTradingStrategyId() !== null) {
			$tradingStrategy = $this->tradingStrategyRepository->findById($request->getTradingStrategyId());
			$bot->changeTradingStrategyId($tradingStrategy->getId());
		}

		if ($request->getTradingStrategySettings() !== null) {
			$bot->changeTradingStrategySettings($request->getTradingStrategySettings());
		}

		if ($request->getStatus() !== null) {
			$bot->changeStatus($request->getStatus());
		}

		$this->botRepository->save($bot);
		return new EditBotResponse($bot);
	}
}