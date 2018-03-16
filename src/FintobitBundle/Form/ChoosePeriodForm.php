<?php


namespace FintobitBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChoosePeriodForm extends AbstractType
{
	private $periods;

	public function __construct()
	{
		$this->periods = new Periods();

	}

	public function buildForm(FormBuilderInterface $builder, array $options)
	{

//		Desired template:
//                      <div class="month-balance__period-title">Choose period:</div>
//                <select class="field-block__select">
//                    <option class="field-block__item">January</option>
//                    <option class="field-block__item">February</option>
//                    <option class="field-block__item">March</option>
//                    <option class="field-block__item">April</option>
//                    <option class="field-block__item">May</option>
//                    <option class="field-block__item">June</option>
//                    <option class="field-block__item">July</option>
//                    <option class="field-block__item">August</option>
//                    <option class="field-block__item">September</option>
//                    <option class="field-block__item">October</option>
//                    <option class="field-block__item">November</option>
//                    <option class="field-block__item">December</option>
//                </select>

		$builder
			->add('period', ChoiceType::class, [
				'label' => false,
				'choices' => $this->periods->getAvailablePeriods(),
				'attr' => [
					'class' => 'field-block__select'
				],
				'label_attr' => [
					'class' => 'month-balance__period-title'
				],
				'choice_attr' => function () {
					return ['class' => 'field-block__item'];
				},
			])
		;
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'csrf_protection' => false,
		]);
	}
}