<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Examples\SimpleEvent;

use Chomenko\AutoInstall\AutoInstall;
use Chomenko\AutoInstall\Config\Tag;
use Chomenko\ExtraForm\Events\IFormEvent;
use Chomenko\ExtraForm\EntityForm;
use Chomenko\ExtraForm\Events\Listener;
use Chomenko\ExtraForm\ExtraForm;
use Nette\Forms\Controls\BaseControl;
use Chomenko\ExtraForm\Controls\ControlEvents;
use Chomenko\ExtraForm\Controls\FormElement;
use Nette\ComponentModel\IComponent;
use Nette\Forms\Controls\TextInput;

/**
 * @Tag({"extraForm.events"})
 */
class Event implements IFormEvent, AutoInstall
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
		}
	}

	/**
	 * CONTROL EVENT
	 * @param BaseControl $control
	 * @param mixed $value
	 */
	public function setValue(BaseControl $control, &$value)
	{
		if ($control instanceof TextInput) {
			$value = "ChangeValue";
		}
	}

}
