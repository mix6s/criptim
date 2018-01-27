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
use Domain\Exchange\Repository\OrderRepositoryInterface;
use Domain\Exchange\UseCase\CreateOrderUseCase;
use Domain\Exchange\UseCase\GetBotTradingSessionBalancesUseCase;
use Domain\Exchange\UseCase\Request\CreateOrderRequest;
use Domain\Exchange\UseCase\Request\GetBotTradingSessionBalancesRequest;
use Domain\Exchange\ValueObject\TradingStrategyId;
use Domain\Exchange\ValueObject\TradingStrategySettings;
use Domain\Policy\DomainCurrenciesPolicy;
use DomainBundle\Exchange\Policy\CryptoMoneyFormatter;
use Money\Currency;
use Money\CurrencyPair;
use Money\Money;
use Money\Number;

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
	/**
	 * @var CreateOrderUseCase
	 */
	private $createOrderUseCase;
	/**
	 * @var GetBotTradingSessionBalancesUseCase
	 */
	private $getBotTradingSessionBalancesUseCase;
	/**
	 * @var OrderRepositoryInterface
	 */
	private $orderRepository;
	private $formatter;
	private $currencies;

	public function __construct(
		BotTradingSessionRepositoryInterface $botTradingSessionRepository,
		BotTradingSessionAccountRepositoryInterface $botTradingSessionAccountRepository,
		GetBotTradingSessionBalancesUseCase $getBotTradingSessionBalancesUseCase,
		BotRepositoryInterface $botRepository,
		ExchangeRepositoryInterface $exchangeRepository,
		CreateOrderUseCase $createOrderUseCase,
		OrderRepositoryInterface $orderRepository
	)
	{
		$this->id = new TradingStrategyId(self::ID);
		$this->botTradingSessionAccountRepository = $botTradingSessionAccountRepository;
		$this->botTradingSessionRepository = $botTradingSessionRepository;
		$this->botRepository = $botRepository;
		$this->exchangeRepository = $exchangeRepository;
		$this->createOrderUseCase = $createOrderUseCase;
		$this->getBotTradingSessionBalancesUseCase = $getBotTradingSessionBalancesUseCase;
		$this->orderRepository = $orderRepository;
		$this->formatter = new CryptoMoneyFormatter();
		$this->currencies = new DomainCurrenciesPolicy();
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


		$balancesRequest = new GetBotTradingSessionBalancesRequest();
		$balancesRequest->setBotTradingSessionId($session->getId());

		$balancesRequest->setCurrency($baseCurrency);
		$baseCurrencyBalances = $this->getBotTradingSessionBalancesUseCase->execute($balancesRequest);

		$balancesRequest->setCurrency($quoteCurrency);
		$quoteCurrencyBalances = $this->getBotTradingSessionBalancesUseCase->execute($balancesRequest);

		$buyOrdersCount = 0;
		$sellOrdersCount = 0;
		$activeOrders = $this->orderRepository->findActive($session->getId());
		foreach ($activeOrders as $order) {
			if ($order->getType() === 'sell') {
				$sellOrdersCount++;
			} else {
				$buyOrdersCount++;
			}
		}

		$minBalance = new Money(0, $baseCurrency);
		if ($baseCurrencyBalances->getAvailableBalance()->greaterThan($minBalance)) {
			/*foreach ($activeOrders as $order) {
				if ($order->getType() === 'buy') {
					continue;
				}
				//$exchange->cancelOrder($order);
			}*/
		}
		$symbolString = $baseCurrency->getCode() . $quoteCurrency->getCode();
		$price = $exchange->getBid($symbolString) * (0.995);


		$ratio = 1 / ($price * (1 + $exchange->getFee()));
		$amountMoney = $this->convert($quoteCurrencyBalances->getAvailableBalance(), $baseCurrency, $ratio);
		if ($amountMoney->lessThan(new Money(1, $baseCurrency))) {
			return;
		}
		$amount = $this->formatter->format($amountMoney);
		$createOrderRequest = new CreateOrderRequest();
		$createOrderRequest->setBotTradingSessionId($session->getId());
		$createOrderRequest->setExchangeId($exchange->getId());
		$createOrderRequest->setSymbol(new CurrencyPair($baseCurrency, $quoteCurrency, 0));

		$createOrderRequest->setType('buy');
		$createOrderRequest->setAmount($amount);
		$createOrderRequest->setPrice($price);

		$order = $this->createOrderUseCase->execute($createOrderRequest)->getOrder();

		var_dump($order);
		//var_dump($baseCurrencyBalances->getAvailableBalance());
		//var_dump($quoteCurrencyBalances->getAvailableBalance());
	}

	/**
	 * @param Money    $money
	 * @param Currency $counterCurrency
	 * @param int      $roundingMode
	 *
	 * @return Money
	 */
	private function convert(Money $money, Currency $counterCurrency, $ratio, $roundingMode = Money::ROUND_DOWN)
	{

		$baseCurrency = $money->getCurrency();
		$baseCurrencySubunit = $this->currencies->subunitFor($baseCurrency);
		$counterCurrencySubunit = $this->currencies->subunitFor($counterCurrency);
		$subunitDifference = $baseCurrencySubunit - $counterCurrencySubunit;

		$ratio = (string) Number::fromString($ratio)->base10($subunitDifference);

		$counterValue = $money->multiply($ratio, $roundingMode);

		return new Money($counterValue->getAmount(), $counterCurrency);
	}
}