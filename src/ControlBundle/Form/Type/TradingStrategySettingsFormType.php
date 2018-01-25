<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/25/18
 * Time: 5:13 PM
 */

namespace ControlBundle\Form\Type;


use Domain\Exchange\ValueObject\TradingStrategySettings;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class TradingStrategySettingsFormType extends TextType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		// When empty_data is explicitly set to an empty string,
		// a string should always be returned when NULL is submitted
		// This gives more control and thus helps preventing some issues
		// with PHP 7 which allows type hinting strings in functions
		// See https://github.com/symfony/symfony/issues/5906#issuecomment-203189375
		$builder->addViewTransformer($this);
	}
	/**
	 * {@inheritdoc}
	 */
	public function transform($data)
	{
		/** @var TradingStrategySettings $data */
		return $data === null ? [] : json_encode($data->getData());
	}

	/**
	 * {@inheritdoc}
	 */
	public function reverseTransform($data)
	{
		$data = json_decode($data, true);
		return new TradingStrategySettings(is_array($data) ? $data  : []);
	}
}