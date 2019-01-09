<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 05.01.2019
 */

namespace Chomenko\ExtraForm\Extend\Pair;

use Chomenko\ExtraForm\Events\IFormEvent;
use Chomenko\ExtraForm\EntityForm;
use Chomenko\ExtraForm\Events\Listener;
use Chomenko\ExtraForm\Extend\Date\DateFormat;
use Chomenko\ExtraForm\ExtraForm;
use Intervention\Image\Constraint;
use Nette\Forms\Controls\BaseControl;
use Chomenko\ExtraForm\Controls\ControlEvents;
use Chomenko\ExtraForm\Controls\FormElement;
use Nette\ComponentModel\IComponent;
use Nette\Forms\Controls\ChoiceControl;
use Nette\Forms\Controls\MultiChoiceControl;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DateEvent implements IFormEvent
{

	/**
	 * @param ExtraForm $form
	 * @param Listener $listener
	 * @return mixed|void
	 */
	public function install(ExtraForm $form, Listener $listener)
	{
		$listener->create(EntityForm::BEFORE_ADD_COMPONENT, [$this, "addComponent"]);
	}

	/**
	 * CONTROL EVENT
	 * @param string $name
	 * @param IComponent $component
	 * @param EntityForm $entityForm
	 */
	public function addComponent(string $name, IComponent $component, EntityForm $entityForm)
	{
		if ($component instanceof FormElement) {
			$listener = $component->getListener();
			$listener->create(ControlEvents::SET_VALUE, [$this, "setValue"]);
			$listener->create(ControlEvents::ADD_CONSTRAINT, [$this, "addConstraint"]);
		}
	}

	/**
	 * @param BaseControl $control
	 * @param Constraint $constraint
	 */
	public function addConstraint(BaseControl $control, $constraint)
	{
		if ($constraint instanceof DateTime) {
			$dateFormat = new DateFormat($constraint->format);
			$control->setOption(DateFormat::OPTION_KEY, $dateFormat);
		}
	}

	/**
	 * CONTROL EVENT
	 * @param BaseControl $control
	 * @param mixed $value
	 */
	public function setValue(BaseControl $control, &$value)
	{
		$date = $control->getOption(DateFormat::OPTION_KEY);
		if (!$date instanceof DateFormat) {
			return;
		}

		if ($value instanceof \DateTime) {
			$value = $value->format($date->getFormat());
		}
	}

}
