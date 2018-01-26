<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/26/18
 * Time: 10:33 AM
 */

namespace ControlBundle\Form\Type;


use Money\Currency;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CurrencyType extends TextType
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
		/** @var Currency $data */
		return $data === null ? null : $data->getCode();
	}

	/**
	 * {@inheritdoc}
	 */
	public function reverseTransform($data)
	{
		if ($data === null) {
			return null;
		}
		return new Currency($data);
	}
}