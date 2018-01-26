<?php
/**
 * Created by PhpStorm.
 * User: mix6s
 * Date: 1/26/18
 * Time: 10:33 AM
 */

namespace ControlBundle\Form\Type;


use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

class IdType extends HiddenType implements DataTransformerInterface
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
	 * Transforms a value from the original representation to a transformed representation.
	 *
	 * This method is called on two occasions inside a form field:
	 *
	 * 1. When the form field is initialized with the data attached from the datasource (object or array).
	 * 2. When data from a request is submitted using {@link Form::submit()} to transform the new input data
	 *    back into the renderable format. For example if you have a date field and submit '2009-10-10'
	 *    you might accept this value because its easily parsed, but the transformer still writes back
	 *    "2009/10/10" onto the form field (for further displaying or other purposes).
	 *
	 * This method must be able to deal with empty values. Usually this will
	 * be NULL, but depending on your implementation other empty values are
	 * possible as well (such as empty strings). The reasoning behind this is
	 * that value transformers must be chainable. If the transform() method
	 * of the first value transformer outputs NULL, the second value transformer
	 * must be able to process that value.
	 *
	 * By convention, transform() should return an empty string if NULL is
	 * passed.
	 *
	 * @param mixed $value The value in the original representation
	 *
	 * @return mixed The value in the transformed representation
	 *
	 * @throws TransformationFailedException when the transformation fails
	 */
	public function transform($value)
	{
		return $value === null ? null : json_encode(['class' => get_class($value), 'value' => (string)$value]);
	}

	/**
	 * Transforms a value from the transformed representation to its original
	 * representation.
	 *
	 * This method is called when {@link Form::submit()} is called to transform the requests tainted data
	 * into an acceptable format for your data processing/model layer.
	 *
	 * This method must be able to deal with empty values. Usually this will
	 * be an empty string, but depending on your implementation other empty
	 * values are possible as well (such as NULL). The reasoning behind
	 * this is that value transformers must be chainable. If the
	 * reverseTransform() method of the first value transformer outputs an
	 * empty string, the second value transformer must be able to process that
	 * value.
	 *
	 * By convention, reverseTransform() should return NULL if an empty string
	 * is passed.
	 *
	 * @param mixed $value The value in the transformed representation
	 *
	 * @return mixed The value in the original representation
	 *
	 * @throws TransformationFailedException when the transformation fails
	 */
	public function reverseTransform($value)
	{
		if ($value === null) {
			return null;
		}
		$data = json_decode($value, true);
		$className = $data['class'];
		return new $className($data['value']);
	}
}