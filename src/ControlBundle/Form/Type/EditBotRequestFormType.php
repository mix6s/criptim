<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/25/18
 * Time: 11:07 AM
 */

namespace ControlBundle\Form\Type;


use Domain\Exchange\Entity\Bot;
use Domain\Exchange\Entity\ExchangeInterface;
use Domain\Exchange\Entity\TradingStrategyInterface;
use Domain\Exchange\UseCase\Request\EditBotRequest;
use DomainBundle\Exchange\Repository\ExchangeRepository;
use DomainBundle\Exchange\Repository\TradingStrategyRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditBotRequestFormType extends CreateBotRequestFormType
{
	/**
	 * @param FormBuilderInterface $builder
	 * @param array $options
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
		->add(
			'botId',
			IdType::class
		)
		->add(
			'status',
			ChoiceType::class,
			[
				'choices' => [
					'Active' => Bot::STATUS_ACTIVE,
					'Inactive' => Bot::STATUS_INACTIVE,
				],
				'label' => 'Status'
			]
		);
		parent::buildForm($builder, $options);
	}

	/**
	 * @param OptionsResolver $resolver
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			[
				'data_class' => EditBotRequest::class,
				'csrf_token_id' => 'EditBotRequest',
				'method' => 'POST'
			]
		);
	}
}