<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\ExtraForm\Validator\Constraints;

use Doctrine\Common\Annotations\Annotation\Target;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 */
class UniqueEntity extends Constraint
{

	/**
	 * @var string
	 */
	public $message = 'This value is already used.';

	/**
	 * @var string
	 */
	public $service = 'extraForm.validator.unique';

	/**
	 * @var array
	 */
	public $fields = [];

	/**
	 * @return array
	 */
	public function getRequiredOptions()
	{
		return ['fields'];
	}

	/**
	 * @return string
	 */
	public function validatedBy()
	{
		return $this->service;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getTargets()
	{
		return self::CLASS_CONSTRAINT;
	}

	/**
	 * @return string
	 */
	public function getDefaultOption()
	{
		return 'fields';
	}

}
