<?php


namespace FintobitBundle;


use DomainBundle\Exchange\Policy\CryptoMoneyFormatter;
use FintobitBundle\Policy\UserMoneyFormatter;
use Money\Money;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TwigExtension extends \Twig_Extension
{

	private $formatter;

	public function __construct(ContainerInterface $container)
	{
		$this->formatter = new UserMoneyFormatter();
	}

	public function getFilters(): array
	{
		return [
			new \Twig_SimpleFilter(
				'fintobitMoneyAmountFormat',
				[$this, 'fintobitMoneyAmountFormatFilter']
			),
			new \Twig_SimpleFilter(
				'fintobitMoneyWithCurrencyFormat',
				[$this, 'fintobitMoneyWithCurrencyFormatFilter']
			),
			new \Twig_SimpleFilter(
				'fintobitMoneyCurrencyFormat',
				[$this, 'fintobitMoneyCurrencyFormatFilter']
			)
		];
	}

	public function fintobitMoneyAmountFormatFilter(Money $money): string
	{
		return $this->formatter->format($money);
	}

	public function fintobitMoneyWithCurrencyFormatFilter(Money $money): string
	{
		return $this->formatter->formatWithCurrency($money);
	}

	public function fintobitMoneyCurrencyFormatFilter(Money $money): string
	{
		return $this->formatter->currency($money);
	}
}