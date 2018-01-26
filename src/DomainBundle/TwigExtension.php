<?php

namespace DomainBundle;

use Domain\Exchange\Entity\Bot;
use Domain\Exchange\UseCase\Request\GetBotExchangeAccountRequest;
use Domain\Exchange\UseCase\Request\GetUserExchangeAccountRequest;
use Domain\Exchange\ValueObject\ExchangeId;
use Domain\Policy\DomainMoneyExchangePolicy;
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
			new \Twig_SimpleFilter('userExchangeBalance', [$this, 'userExchangeBalanceFilter']),
			new \Twig_SimpleFilter('moneyFormat', [$this, 'moneyFormatFilter']),
		];
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
		return $this->formatter->format($money);
	}
}