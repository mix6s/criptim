<?php

namespace DomainBundle;

use Domain\Exception\EntityNotFoundException;
use Domain\Exchange\Entity\Bot;
use Domain\Exchange\UseCase\Request\GetBotExchangeAccountRequest;
use Domain\Exchange\UseCase\Request\GetBotTradingSessionBalancesRequest;
use Domain\Exchange\UseCase\Request\GetUserExchangeAccountRequest;
use Domain\Exchange\ValueObject\ExchangeId;
use Domain\ValueObject\UserId;
use DomainBundle\Exchange\Policy\CryptoMoneyFormatter;
use Money\Currency;
use Money\Money;
use Symfony\Component\DependencyInjection\Container;


/**
 * Class TwigExtension
 * @package DomainBundle
 */
class TwigExtension extends \Twig_Extension
{
	private $container;
	private $formatter;

	/**
	 * TwigAppExtension constructor.
	 * @param Container $container
	 */
	public function __construct(Container $container)
	{
		$this->formatter = new CryptoMoneyFormatter();
		$this->container = $container;
	}
	/**
	 * @return array
	 */
	public function getFilters()
	{
		return [
			new \Twig_SimpleFilter('botBalance', [$this, 'botBalanceFilter']),
			new \Twig_SimpleFilter('botSessionBalances', [$this, 'botSessionBalancesFilter']),
			new \Twig_SimpleFilter('userExchangeBalance', [$this, 'userExchangeBalanceFilter']),
			new \Twig_SimpleFilter('moneyFormat', [$this, 'moneyFormatFilter']),
		];
	}

	public function botSessionBalancesFilter(Bot $bot, string $currency)
	{
		try {
			$session = $this->container->get('ORM\BotTradingSessionRepository')->findLastByBotId($bot->getId());
		} catch (EntityNotFoundException $e) {
			return null;
		}


		$request = new GetBotTradingSessionBalancesRequest();
		$request->setCurrency(new Currency($currency));
		$request->setBotTradingSessionId($session->getId());
		$balances = $this->container->get('UseCase\GetBotTradingSessionBalancesUseCase')->execute($request);
		return $balances;
	}

	public function botBalanceFilter(Bot $bot, string $currency)
	{
		$request = new GetBotExchangeAccountRequest();
		$request->setCurrency(new Currency($currency));
		$request->setBotId($bot->getId());
		$request->setExchangeId($bot->getExchangeId());
		$account = $this->container->get('UseCase\GetBotExchangeAccountUseCase')->execute($request)->getBotExchangeAccount();
		return $account->getBalance();
	}

	public function userExchangeBalanceFilter(UserId $userId, ExchangeId $exchangeId, string $currency)
	{
		if ($userId->isEmpty()) {
			return new Money(0, new Currency($currency));
		}
		$request = new GetUserExchangeAccountRequest();
		$request->setCurrency(new Currency($currency));
		$request->setUserId($userId);
		$request->setExchangeId($exchangeId);
		$account = $this->container->get('UseCase\GetUserExchangeAccountUseCase')->execute($request)->getUserExchangeAccount();
		return $account->getBalance();
	}

	public function moneyFormatFilter(Money $money)
	{
		return $this->formatter->formatWithCurrency($money);
	}
}