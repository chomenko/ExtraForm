<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\ExtraForm;

use Chomenko\ExtraForm\Events\IFormEvent;
use Chomenko\ExtraForm\Events\Listener;
use Nette;
use Nette\ComponentModel\IComponent;
use Nette\ComponentModel\IContainer;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\BaseControl;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
	 * @var TranslatorWrapped
	 */
	protected $translatorWrapped;

	/**
	 * @var ValidatorInterface
	 */
	private $validator;

	/**
	 * @var array|null
	 */
	protected $captchaConfig;

	/**
	 * @param IContainer|null $parent
	 * @param string|null $name
	 * @param FormEvents|null $formEvents
	 */
	public function __construct(IContainer $parent = NULL, $name = NULL, FormEvents $formEvents = NULL)
	{
		$this->eventsListener = new Listener();
		$this->translatorWrapped = new TranslatorWrapped($this);
		$this->validator = Validation::createValidator();

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
	 * @return null|string
	 */
	public function getTranslateFile(): ?string
	{
		return $this->translatorWrapped->getTranslateFile();
	}

	/**
	 * @param null|string $translateFile
	 */
	public function setTranslateFile($translateFile)
	{
		$this->translatorWrapped->setTranslateFile($translateFile);
	}

	/**
	 * @param Nette\Localization\ITranslator|NULL $translator
	 * @return static
	 */
	public function setTranslator(Nette\Localization\ITranslator $translator = NULL)
	{
		$this->translatorWrapped->setTranslator($translator);
		return parent::setTranslator($this->translatorWrapped);
	}

	/**
	 * @param string|Nette\Utils\Html $message
	 * @param null $parameters
	 * @param bool $translate
	 */
	public function addError($message, $parameters = NULL, $translate = TRUE)
	{
		if ($translate) {
			$message = $this->translatorWrapped->translate($message, $parameters);
		}
		parent::addError($message, FALSE);
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

	public function fireEvents()
	{
		if (!$this->isSubmitted()) {
			return;

		}

		if ($this->hasOnlyValidation()) {
			if (!$this->getErrors()) {
				$this->validate();
			}
			if (!$this->getPresenter()->isAjax()) {
				return;
			}
			$payload = $this->getValidatePayload();
			$this->getPresenter()->payload->formsValidation[] = $payload;
			$this->getPresenter()->sendPayload();
			return;
		}
		parent::fireEvents();
	}

	/**
	 * @return bool
	 */
	public function hasOnlyValidation(): bool
	{
		$onlyValidation = $this->getHttpData($this::DATA_TEXT, "_validate");
		return filter_var($onlyValidation, FILTER_VALIDATE_BOOLEAN);
	}

	/**
	 * @return array
	 */
	protected function getValidatePayload(): array
	{
		$payload = [
			"name" => $this->getName(),
			"id" => $this->getElementPrototype()->getAttribute("id"),
			"errors" => [],
			"fields" => [],
		];

		foreach ($this->getOwnErrors() as $error) {
			$payload['errors'][] = (string)$error;
		}

		foreach ($this->getComponents(TRUE) as $component) {
			if (!$component instanceof BaseControl || !$component->hasErrors()) {
				continue;
			}

			$field = [
				"name" => $component->getName(),
				"htmlName" => $component->getHtmlName(),
				"errors" => []
			];

			foreach ($component->getErrors() as $error) {
				$field["errors"][] = (string)$error;
			}
			$payload['fields'][] = $field;
		}
		return $payload;
	}

	/**
	 * @param ConstraintViolationListInterface $errors
	 * @param Component $component
	 */
	public function addConstraintErrors(ConstraintViolationListInterface $errors, Nette\ComponentModel\Component $component = NULL)
	{
		/** @var ConstraintViolation $error */
		foreach ($errors as $error) {
			$params = [];
			$parameters = $error->getParameters();
			if ($parameters && is_array($parameters)) {
				foreach ($parameters as $key => $value) {
					$key = str_replace(["{{", " ", "}}"], "", $key);
					$params[$key] = $value;
				}
			}

			$message = $this->translatorWrapped->translate($error->getMessageTemplate(), $params);
			if ($component) {
				$component->addError($message, FALSE);
			} else {
				$this->addError($message, FALSE);
			}
		}
	}

	/**
	 * @param string $name
	 * @param null|string|object $label
	 * @param null $cols
	 * @param null $maxLength
	 * @return Controls\TextInput
	 */
	public function addText($name, $label = NULL, $cols = NULL, $maxLength = NULL)
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
	public function addPassword($name, $label = NULL, $cols = NULL, $maxLength = NULL)
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
	public function addTextArea($name, $label = NULL, $cols = NULL, $rows = NULL)
	{
		$component = new Controls\TextArea($label);
		$component->setHtmlAttribute('cols', $cols);
		$component->setHtmlAttribute('rows', $rows);
		$this->addComponent($component, $name);
		$component->installed($this);
		return $component;
	}


	/**
	 * @param string $name
	 * @param null|string|object $label
	 * @return Controls\TextInput
	 */
	public function addEmail($name, $label = NULL)
	{
		$component = new Controls\TextInput($label);
		$component->setRequired(FALSE);
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
	public function addInteger($name, $label = NULL)
	{
		$component = new Controls\TextInput($label);
		$component->setNullable();
		$component->setRequired(FALSE);
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
	public function addUpload($name, $label = NULL, $multiple = FALSE)
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
	public function addMultiUpload($name, $label = NULL)
	{
		$component = new Controls\UploadControl($label, TRUE);
		$this->addComponent($component, $name);
		$component->installed($this);
		return $component;
	}

	/**
	 * @param string $name
	 * @param null|string|object $caption
	 * @return Controls\Checkbox
	 */
	public function addCheckbox($name, $caption = NULL)
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
	public function addRadioList($name, $label = NULL, $items = [])
	{
		$component = new Controls\RadioList($label);
		$this->addComponent($component, $name, NULL, FALSE);
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
	public function addCheckboxList($name, $label = NULL, $items = [])
	{
		$component = new Controls\CheckboxList($label);
		$this->addComponent($component, $name, NULL);
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
	public function addSelect($name, $label = NULL, $items = [], $size = NULL)
	{
		$component = new Controls\SelectBox($label);
		$component->setHtmlAttribute('size', $size > 1 ? (int)$size : NULL);
		$this->addComponent($component, $name, NULL);
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
	public function addMultiSelect($name, $label = NULL, $items = [], $size = NULL)
	{
		$component = new Controls\MultiSelectBox($label);
		$component->setHtmlAttribute('size', $size > 1 ? (int)$size : NULL);
		$this->addComponent($component, $name, NULL);
		$component->setItems($items);
		$component->installed($this);
		return $component;
	}

	/**
	 * @param string $name
	 * @param null $caption
	 * @return Controls\SubmitButton
	 */
	public function addSubmit($name, $caption = NULL)
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
	public function addButton($name, $caption = NULL)
	{
		$component = new Controls\Button($caption);
		$html = $component->getControlPrototype();
		$html->setName('button');
		$this->addComponent($component, $name);
		$component->installed($this);
		return $component;
	}

	/**
	 * @param string $name
	 * @param string|NULL $src
	 * @param null $alt
	 * @return Controls\ImageButton
	 */
	public function addImage($name, $src = NULL, $alt = NULL)
	{
		$component = new Controls\ImageButton($src, $alt);
		$this->addComponent($component, $name);
		$component->installed($this);
		return $component;
	}

	/**
	 * @param string $name
	 * @param string|array $destination
	 * @param string|null $caption
	 * @return Controls\LinkButton
	 */
	public function addLinkButton(string $name, $destination, $caption = NULL)
	{
		$component = new Controls\LinkButton($destination, $caption);
		$html = $component->getControlPrototype();
		$html->setName('a');
		$this->addComponent($component, $name);
		$component->installed($this);
		return $component;
	}


	/**
	 * @param string $name
	 * @param null $default
	 * @return Controls\HiddenField
	 */
	public function addHidden($name, $default = NULL)
	{
		$component = new Controls\HiddenField($default);
		$this->addComponent($component, $name);
		$component->installed($this);
		return $component;
	}
	
	
	public function addRecaptcha($name, string $domainKey, string $secretKey, ?int $tryCount = 5, bool $enable = true)
	{
		$component = new Controls\Recaptcha($domainKey, $secretKey, $tryCount, $enable);
		$component->setRequired(FALSE);
		$this->addComponent($component, $name);
		$component->installed($this);
		return $component;
	}

	/**
	 * @return ValidatorInterface
	 */
	public function getValidator(): ValidatorInterface
	{
		return $this->validator;
	}

	/**
	 * @param ValidatorInterface $validator
	 * @return $this
	 */
	public function setValidator($validator)
	{
		$this->validator = $validator;
		return $this;
	}

	protected function receiveHttpData()
	{
		$data = parent::receiveHttpData();

		$presenter = $this->getPresenter();
		if (!$presenter->isSignalReceiver($this, 'submit')) {
			return;
		}

		if ($data) {
			return $data;
		}
		$presenter = $this->getPresenter();
		$request = $presenter->getRequest();

		if ($request->isMethod('forward') && $request->hasFlag('post')) {
			return \Nette\Utils\Arrays::mergeTree($request->getPost(), $request->getFiles());
		}
		return;
	}

}
