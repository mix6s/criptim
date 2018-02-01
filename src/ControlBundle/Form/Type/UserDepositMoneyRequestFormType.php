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
use Domain\Exchange\UseCase\Request\UserDepositMoneyRequest;
use Domain\Exchange\ValueObject\ExchangeId;
use Domain\ValueObject\Id;
use DomainBundle\Exchange\Repository\ExchangeRepository;
use DomainBundle\Exchange\Repository\TradingStrategyRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserDepositMoneyRequestFormType extends AbstractType
{
	/**
	 * @var ExchangeRepository
	 */
	private $exchangeRepository;

	public function __construct(
		ExchangeRepository $exchangeRepository
	) {
		$this->exchangeRepository = $exchangeRepository;
	}

	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add(
				'userId',
				IdType::class,
				[

				]
			)
			->add(
				'exchangeId',
				ChoiceType::class,
				[
					'choice_loader' => new CallbackChoiceLoader(function () {
						return array_map(function (ExchangeInterface $exchange) {
							return $exchange->getId();
						}, $this->exchangeRepository->findAll());
					}),
					'choice_value' => function (ExchangeId $exchangeId = null) {
						return $exchangeId;
					},
					'label' => 'Exchange'
				]
			)
			->add(
				'amount',
				TextType::class,
				[

					'label' => 'Amount'
				]
			)
			->add(
				'currency',
				CurrencyType::class,
				[

				]
			)
			->add(
				'Save',
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
				'data_class' => UserDepositMoneyRequest::class,
				'csrf_token_id' => 'UserDepositMoneyRequest',
				'method' => 'POST'
			]
		);
	}
}