<?php
/**
 * Created by PhpStorm.
 * User: Mix6s
 * Date: 22.07.2017
 * Time: 11:53
 */

namespace AppBundle\Form\Type;


use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationFormType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add(
				'email',
				EmailType::class,
				[
				]
			)
			->add(
				'plainPassword',
				RepeatedType::class,
				[
					'type' => PasswordType::class,
					'first_options' => ['label' => 'form.password'],
					'second_options' => ['label' => 'form.password_confirmation'],
					'invalid_message' => 'fos_user.password.mismatch',
				]
			);

	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(
			[
				'data_class' => User::class,
				'csrf_token_id' => 'registration',
			]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return 'app_user_registration';
	}
}