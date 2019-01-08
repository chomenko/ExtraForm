<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 01.06.2018 17:19
 */

namespace Chomenko\ExtraForm;

use Chomenko\ExtraForm\Events\IFormEvent;
use Chomenko\ExtraForm\Events\Listener;
use Nette\ComponentModel\IComponent;
use Nette\ComponentModel\IContainer;
use Nette\Application\UI\Form;

/**
 * Class ExtraForm
 * @property-read Render $renderer
 * @package Chomenko\ExtraForm
 */
class ExtraForm extends Form
{

	const CRETE_FORM = "createForm";
	const BEFORE_ADD_COMPONENT = "beforeAddComponent";
	const ADD_COMPONENT = "addComponent";
	const BEFORE_RENDER = "beforeRender";
	const AFTER_RENDER = "afterRender";

    /**
     * @var Builder
     */
    protected $builder;

	/**
	 * @var Listener
	 */
    protected $eventsListener;

	/**
	 * @param IContainer|NULL $parent
	 * @param null $name
	 * @param FormEvents|NULL $formEvents
	 */
    public function __construct(IContainer $parent = null, $name = null, FormEvents $formEvents = null)
    {
    	$this->eventsListener = new Listener();
        parent::__construct($parent, $name);

        if ($formEvents) {
        	foreach ($formEvents->getEvents() as $event) {
				if (!$event instanceof IFormEvent) {
					continue;
				}
				$event->install($this, $this->eventsListener);
			}
		}

        $render = new Render($this);
        $this->builder = $render->builder();
        $this->setRenderer($render);
		$this->eventsListener->emit(self::CRETE_FORM, $this);
    }

	/**
	 * @param IComponent $component
	 * @param int|null|string $name
	 * @param null $insertBefore
	 * @return static
	 */
    public function addComponent(IComponent $component, $name, $insertBefore = NULL)
	{
		$this->eventsListener->emit(self::BEFORE_ADD_COMPONENT, $name, $component, $this);
		$container = parent::addComponent($component, $name, $insertBefore);
		$this->eventsListener->emit(self::ADD_COMPONENT, $component, $this);
		return $container;
	}

	/**
	 * @return Listener
	 */
	public function getEventsListener(): Listener
	{
		return $this->eventsListener;
	}

	/**
     * @return Builder
     */
    public function builder()
    {
        return $this->builder;
    }

	/**
	 * @param string $name
	 * @param null|string|object $label
	 * @param null $cols
	 * @param null $maxLength
	 * @return Controls\TextInput
	 */
    public function addText($name, $label = null, $cols = null, $maxLength = null)
    {
		$component = new Controls\TextInput($label, $maxLength);
		$component->setHtmlAttribute('size', $cols);
    	$this->addComponent($component, $name);
		$component->installed($this);
		return $component;
    }

	/**
	 * @param string $name
	 * @param null|string|object $label
	 * @param null $cols
	 * @param null $maxLength
	 * @return Controls\TextInput
	 */
    public function addPassword($name, $label = null, $cols = null, $maxLength = null)
    {
		$component = new Controls\TextInput($label, $maxLength);
		$component->setHtmlAttribute('size', $cols);
		$component->setHtmlType('password');
		$this->addComponent($component, $name);
		$component->installed($this);
        return $component;
    }

	/**
	 * @param string $name
	 * @param null|string|object $label
	 * @param null $cols
	 * @param null $rows
	 * @return Controls\TextArea
	 */
    public function addTextArea($name, $label = null, $cols = null, $rows = null)
    {
		$component = new Controls\TextArea($label);
		$component->setHtmlAttribute('cols', $cols);
		$component->setHtmlAttribute('rows', $rows);
		$this->addComponent($component, $name);
		$component->installed($this);
		return $component;
    }


    /**
     * Adds input for email.
     * @param string $name
     * @param null|string|object object
     * @return Controls\TextInput
     */
    public function addEmail($name, $label = null)
    {
		$component = new Controls\TextInput($label);
		$component->setRequired(false);
		$component->addRule(Form::EMAIL);
		$this->addComponent($component, $name);
		$component->installed($this);
		return $component;
    }

	/**
	 * @param string $name
	 * @param null|string|object $label
	 * @return Controls\TextInput
	 */
    public function addInteger($name, $label = null)
	{
		$component = new Controls\TextInput($label);
		$component->setNullable();
		$component->setRequired(false);
		$component->addRule(Form::INTEGER);
		$this->addComponent($component, $name);
		$component->installed($this);
		return $component;
    }

	/**
	 * @param string $name
	 * @param null|string|object $label
	 * @param bool $multiple
	 * @return Controls\UploadControl
	 */
    public function addUpload($name, $label = null, $multiple = false)
    {
		$component = new Controls\UploadControl($label, $multiple);
		$this->addComponent($component, $name);
		$component->installed($this);
		return $component;
    }

	/**
	 * @param string $name
	 * @param null|string|object $label
	 * @return Controls\UploadControl
	 */
    public function addMultiUpload($name, $label = null)
    {
		$component = new  Controls\UploadControl($label, true);
		$this->addComponent($component, $name);
		$component->installed($this);
		return $component;
    }

	/**
	 * @param string $name
	 * @param null|string|object $caption
	 * @return Controls\Checkbox
	 */
    public function addCheckbox($name, $caption = null)
    {
		$component = new Controls\Checkbox($caption);
		$this->addComponent($component, $name);
		$component->installed($this);
		return $component;
    }

	/**
	 * @param string $name
	 * @param null|string|object $label
	 * @param array|object $items
	 * @return Controls\RadioList
	 */
    public function addRadioList($name, $label = null, $items = [])
    {
		$component = new Controls\RadioList($label);
		$this->addComponent($component, $name, null, false);
		$component->setItems($items);
		$component->installed($this);
		return $component;
    }

	/**
	 * @param string $name
	 * @param null|string|object $label
	 * @param array|object $items
	 * @return Controls\CheckboxList
	 */
    public function addCheckboxList($name, $label = null, $items = [])
    {
		$component = new Controls\CheckboxList($label);
		$this->addComponent($component, $name, null);
		$component->setItems($items);
		$component->installed($this);
		return $component;
    }

	/**
	 * @param string $name
	 * @param null|string|object $label
	 * @param array|object $items
	 * @param null $size
	 * @return Controls\SelectBox
	 */
    public function addSelect($name, $label = null, $items = [], $size = null)
    {
		$component = new Controls\SelectBox($label);
		$component->setHtmlAttribute('size', $size > 1 ? (int) $size : null);
		$this->addComponent($component, $name, null);
		$component->setItems($items);
		$component->installed($this);
		return $component;
    }

	/**
	 * @param string $name
	 * @param null|string|object $label
	 * @param array|object $items
	 * @param null $size
	 * @return Controls\MultiSelectBox
	 */
    public function addMultiSelect($name, $label = null, $items = [], $size = null)
    {
		$component = new Controls\MultiSelectBox($label);
		$component->setHtmlAttribute('size', $size > 1 ? (int) $size : null);
		$this->addComponent($component, $name, null);
		$component->setItems($items);
		$component->installed($this);
		return $component;
    }

	/**
	 * @param string $name
	 * @param null $caption
	 * @return Controls\SubmitButton
	 */
    public function addSubmit($name, $caption = null)
    {
		$component = new Controls\SubmitButton($caption);
        $html = $component->getControlPrototype();
        $html->setName('button');
		$this->addComponent($component, $name);
		$component->installed($this);
        return $component;
    }

	/**
	 * @param string $name
	 * @param null|string|object $caption
	 * @return Controls\Button
	 */
    public function addButton($name, $caption = null)
    {
		$component = new Controls\Button($caption);
        $html = $component->getControlPrototype();
        $html->setName('button');
		$this->addComponent($component, $name);
		$component->installed($this);
        return  $component;
    }

	/**
	 * @param string $name
	 * @param string|NULL $src
	 * @param null $alt
	 * @return Controls\ImageButton
	 */
    public function addImage($name, $src = null, $alt = null)
    {
		$component = new Controls\ImageButton($src, $alt);
		$this->addComponent($component, $name);
		$component->installed($this);
		return  $component;
    }

	/**
	 * @param $name
	 * @param null $default
	 * @return Controls\HiddenField
	 */
    public function addHidden($name, $default = NULL)
	{
		$component = new Controls\HiddenField($name, $default);
		$this->addComponent($component, $name);
		$component->installed($this);
		return  $component;
	}

}
