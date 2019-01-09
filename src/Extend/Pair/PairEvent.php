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
use Chomenko\ExtraForm\ExtraForm;
use Nette\Forms\Controls\BaseControl;
use Chomenko\ExtraForm\Controls\ControlEvents;
use Chomenko\ExtraForm\Controls\FormElement;
use Nette\ComponentModel\IComponent;
use Nette\Forms\Controls\ChoiceControl;
use Nette\Forms\Controls\MultiChoiceControl;

class PairEvent implements IFormEvent
{

	/**
	 * @param ExtraForm $form
	 * @param Listener $listener
	 * @return mixed|void
	 */
	public function install(ExtraForm $form, Listener $listener)
	{
		if ($form instanceof EntityForm) {
			$listener->create(EntityForm::BEFORE_ADD_COMPONENT, [$this, "addComponent"]);
		}
	}

	/**
	 * CONTROL EVENT
	 * @param string $name
	 * @param IComponent $component
	 * @param EntityForm $entityForm
	 */
	public function addComponent(string $name, IComponent $component, EntityForm $entityForm)
	{
		if (!($component instanceof FormElement && $entityForm->hasEntityField($name))) {
			return;
		}
		$listener = $component->getListener();
		if($component instanceof ChoiceControl || $component instanceof MultiChoiceControl) {
			$listener->create(ControlEvents::SET_ITEMS, [$this, "setPairItems"]);
			$listener->create(ControlEvents::SET_VALUE, [$this, "setPairValue"]);
		}
	}

	/**
	 * CONTROL EVENT
	 * @param BaseControl $component
	 * @param $items
	 * @throws \Chomenko\ExtraForm\Exception\Exception
	 */
	public function setPairItems(BaseControl $component, &$items)
	{
		if (is_array($items) && !empty($items)) {
			$key = array_keys($items)[0];
			$firstItem = $items[$key];
			if (is_object($firstItem)) {
				$pairs = new Pairs(get_class($firstItem));
				$pairs->setItems($items);
				$items = $pairs;
			}
		}
		if ($items instanceof IPairs) {
			$component->setOption(IPairs::OPTION_KEY, $items);
			$items = $items->create();
		}
	}

	/**
	 * CONTROL EVENT
	 * @param BaseControl $control
	 * @param mixed $value
	 */
	public function setPairValue(BaseControl $control, &$value)
	{
		$pair = $control->getOption(IPairs::OPTION_KEY);
		if (!$pair instanceof IPairs) {
			return;
		}
		$pair->setSelected($value);
	}

}
