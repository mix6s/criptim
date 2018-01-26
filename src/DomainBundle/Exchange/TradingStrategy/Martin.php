<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/25/18
 * Time: 10:23 AM
 */

namespace DomainBundle\Exchange\TradingStrategy;


use Domain\Exchange\Entity\BotTradingSession;
use Domain\Exchange\Entity\TradingStrategyInterface;
use Domain\Exchange\Repository\BotRepositoryInterface;
use Domain\Exchange\Repository\BotTradingSessionAccountRepositoryInterface;
use Domain\Exchange\Repository\BotTradingSessionRepositoryInterface;
use Domain\Exchange\Repository\ExchangeRepositoryInterface;
use Domain\Exchange\UseCase\GetBotTradingSessionAccountUseCase;
use Domain\Exchange\UseCase\Request\GetBotTradingSessionAccountRequest;
use Domain\Exchange\ValueObject\TradingStrategyId;
use Domain\Exchange\ValueObject\TradingStrategySettings;
use Money\Currency;

class Martin implements TradingStrategyInterface
{
	const ID = 'martin';

	/**
	 * @var TradingStrategyId
	 */
	private $id;
	/**
	 * @var BotTradingSessionAccountRepositoryInterface
	 */
	private $botTradingSessionAccountRepository;
	/**
	 * @var GetBotTradingSessionAccountUseCase
	 */
	private $getBotTradingSessionAccountUseCase;
	/**
	 * @var BotTradingSessionRepositoryInterface
	 */
	private $botTradingSessionRepository;
	/**
	 * @var BotRepositoryInterface
	 */
	private $botRepository;
	/**
	 * @var ExchangeRepositoryInterface
	 */
	private $exchangeRepository;

	public function __construct(
		BotTradingSessionRepositoryInterface $botTradingSessionRepository,
		BotTradingSessionAccountRepositoryInterface $botTradingSessionAccountRepository,
		GetBotTradingSessionAccountUseCase $getBotTradingSessionAccountUseCase,
		BotRepositoryInterface $botRepository,
		ExchangeRepositoryInterface $exchangeRepository
	)
	{
		$this->id = new TradingStrategyId(self::ID);
		$this->botTradingSessionAccountRepository = $botTradingSessionAccountRepository;
		$this->getBotTradingSessionAccountUseCase = $getBotTradingSessionAccountUseCase;
		$this->botTradingSessionRepository = $botTradingSessionRepository;
		$this->botRepository = $botRepository;
		$this->exchangeRepository = $exchangeRepository;
	}

	public function getId(): TradingStrategyId
	{
		return $this->id;
	}

	public function isNeedToStartTrading(TradingStrategySettings $settings): bool
	{
		return true;
	}

	public function processTrading(BotTradingSession $session)
	{
		$botId = $session->getBotId();
		$bot = $this->botRepository->findById($botId);
		$exchange = $this->exchangeRepository->findById($bot->getExchangeId());

		$settings = $session->getTradingStrategySettings()->getData();
		$baseCurrency = new Currency($settings['baseCurrency']);
		$quoteCurrency = new Currency($settings['quoteCurrency']);

		$getAccountRequest = new GetBotTradingSessionAccountRequest();
		$getAccountRequest->setBotTradingSessionId($session->getId());

		$getAccountRequest->setCurrency($baseCurrency);
		$baseCurrencyAccount = $this->getBotTradingSessionAccountUseCase->execute($getAccountRequest)->getBotTradingSessionAccount();

		$getAccountRequest->setCurrency($quoteCurrency);
		$quoteCurrencyAccount = $this->getBotTradingSessionAccountUseCase->execute($getAccountRequest)->getBotTradingSessionAccount();



		var_dump($baseCurrencyAccount->getBalance());
		var_dump($quoteCurrencyAccount->getBalance());
	}
}