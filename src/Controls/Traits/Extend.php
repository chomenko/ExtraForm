<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\ExtraForm\Controls\Traits;

use Chomenko\ExtraForm\Builds\HtmlUtility;
use Chomenko\ExtraForm\Controls\ControlEvents;
use Chomenko\ExtraForm\EntityForm;
use Chomenko\ExtraForm\Events\Listener;
use Symfony\Component\Validator\Constraint;
use Chomenko\ExtraForm\ExtraForm;
use Nette\InvalidArgumentException;
use Nette\Utils\Html;

/**
 * @property-read ExtraForm|EntityForm $form
 */
trait Extend
{

	/**
	 * @var Listener
	 */
	protected $evenListener;

	/**
	 * @var Constraint[]
	 */
	protected $constraints = [];

	/**
	 * @var bool
	 */
	protected $usedDefaultValue = FALSE;

	/**
	 * @param string $value
	 * @param null|array|string $count
	 * @param bool $translateModal
	 * @return mixed
	 */
	public function translate($value, $count = NULL, $translateModal = TRUE)
	{
		if ($translator = $this->getTranslator()) {
			$tmp = is_array($value) ? [&$value] : [[&$value]];
			foreach ($tmp[0] as &$v) {
				if ($v != NULL && !$v instanceof Html) { // intentionally ==
					$v = $translator->translate($v, $count, $this->getForm()->getTranslateFile(), $translateModal);
				}
			}
		}
		return $value;
	}

	/**
	 * @param  bool $throw
	 * @return ExtraForm|null
	 */
	public function getForm($throw = TRUE): ?ExtraForm
	{
		return parent::getForm($throw);
	}

	/**
	 * @param ExtraForm $form
	 */
	public function attached($form)
	{
		parent::attached($form);
		$this->evenListener->emit(ControlEvents::ATTACHED, $this, $form);
	}

	/**
	 * @param ExtraForm $form
	 */
	public function installed($form)
	{
		$this->evenListener->emit(ControlEvents::INSTALLED, $this, $form);
	}

	/**
	 * @param mixed $value
	 */
	public function setValue($value)
	{
		$this->evenListener->emit(ControlEvents::SET_VALUE, $this, $value);

		if (empty($value)) {
			try {
				parent::setValue($value);
			} catch (InvalidArgumentException $e) {
				parent::setValue(NULL);
			}
		} else {
			parent::setValue($value);
		}
	}

	/**
	 * Loads HTTP data.
	 * @return void
	 */
	public function loadHttpData()
	{
		$this->evenListener->emit(ControlEvents::LOAD_HTTP_DATA, $this);
		parent::loadHttpData();
		$this->evenListener->emit(ControlEvents::AFTER_LOAD_HTTP_DATA, $this);
	}

	/**
	 * @param Html $el
	 */
	protected function setErrorClass(Html $el)
	{
		if ($this->hasErrors()) {
			HtmlUtility::addClass($el, 'is-invalid');
		}
	}

	/**
	 * @return Html
	 */
	public function render()
	{
		/** @var ExtraForm $form */
		$form = $this->getForm();
		$html = $form->builder()->getContentByItemName($form, $this->getName());
		$this->evenListener->emit(ControlEvents::RENDER, $this, $html);
		return $html;
	}

	/**
	 * @return Listener
	 */
	public function getListener(): Listener
	{
		return $this->evenListener;
	}

	/**
	 * @param mixed$key
	 * @param mixed $value
	 * @return $this
	 */
	public function setOption($key, $value)
	{
		parent::setOption($key, $value);
		$this->evenListener->emit(ControlEvents::SET_OPTION, $this, $key, $value);
		return $this;
	}

	/**
	 * @param Constraint|Constraint[] $constraint
	 */
	public function addConstraint($constraint)
	{
		$this->constraints[] = $constraint;
		$this->evenListener->emit(ControlEvents::ADD_CONSTRAINT, $this, $constraint);
	}

	public function validate()
	{
		parent::validate();
		foreach ($this->constraints as $constraint) {
			$form = $this->form;
			$validator = $form->getValidator();
			if ($form instanceof ExtraForm) {
				$validator = $form->getValidatorByConstraint($constraint);
			}
			if ($errors = $validator->validate($this->getValue(), $constraint)) {
				$this->form->addConstraintErrors($errors, $this);
			}
		}
	}

	/**
	 * @param mixed $value
	 * @return static
	 */
	public function setDefaultValue($value)
	{
		parent::setDefaultValue($value);
		$this->usedDefaultValue = TRUE;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isUsedDefaultValue(): bool
	{
		return $this->usedDefaultValue;
	}

}
