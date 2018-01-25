<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/25/18
 * Time: 11:07 AM
 */

namespace ControlBundle\Form\Type;


use Domain\Exchange\Entity\ExchangeInterface;
use Domain\Exchange\Entity\TradingStrategyInterface;
use Domain\Exchange\UseCase\Request\CreateBotRequest;
use DomainBundle\Exchange\Repository\ExchangeRepository;
use DomainBundle\Exchange\Repository\TradingStrategyRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateBotRequestFormType extends AbstractType
{
	/**
	 * @var ExchangeRepository
	 */
	private $exchangeRepository;
	/**
	 * @var TradingStrategyRepository
	 */
	private $tradingStrategyRepository;

	public function __construct(
		ExchangeRepository $exchangeRepository,
		TradingStrategyRepository $tradingStrategyRepository
	) {
		$this->exchangeRepository = $exchangeRepository;
		$this->tradingStrategyRepository = $tradingStrategyRepository;
	}

	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add(
				'exchangeId',
				ChoiceType::class,
				[
					'choice_loader' => new CallbackChoiceLoader(function () {
						return array_map(function (ExchangeInterface $exchange) {
							return $exchange->getId();
						}, $this->exchangeRepository->findAll());
					}),
					'label' => 'Биржа'
				]
			)
			->add(
				'tradingStrategyId',
				ChoiceType::class,
				[
					'choice_loader' => new CallbackChoiceLoader(function () {
						return array_map(function (TradingStrategyInterface $strategy) {
							return $strategy->getId();
						}, $this->tradingStrategyRepository->findAll());
					}),
					'label' => 'Стратегия'
				]
			)
			->add(
				'tradingStrategySettings',
				TradingStrategySettingsFormType::class,
				[
					'label' => 'Параметры'
				]
			)
			->add(
				'Создать',
				SubmitType::class,
				[
				]
			);
	}

	/**
	 * @param OptionsResolver $resolver
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			[
				'data_class' => CreateBotRequest::class,
				'csrf_token_id' => 'CreateBotRequest',
				'method' => 'POST'
			]
		);
	}
}