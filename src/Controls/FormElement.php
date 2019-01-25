<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\ExtraForm\Controls;

use Chomenko\ExtraForm\Events\Listener;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Validator\ValidatorInterface;

interface FormElement
{

	/**
	 * @return Listener
	 */
	public function getListener(): Listener;

	/**
	 * @param Constraint|Constraint[] $constraint
	 */
	public function addConstraint($constraint);

}
