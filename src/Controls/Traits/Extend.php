<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 01.06.2018 21:43
 */

namespace Chomenko\ExtraForm\Controls\Traits;

use Chomenko\ExtraForm\Builds\HtmlUtility;
use Chomenko\ExtraForm\Controls\ControlEvents;
use Chomenko\ExtraForm\Events\Listener;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validation;
use Chomenko\ExtraForm\ExtraForm;
use Nette\InvalidArgumentException;
use Nette\Utils\Html;

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
     * @param $value
     * @param null $count
     * @param bool $translate_modal
     * @return mixed
     */
    public function translate($value, $count = null, $translate_modal = true)
    {
        if ($translator = $this->getTranslator()) {
            $tmp = is_array($value) ? [&$value] : [[&$value]];
            foreach ($tmp[0] as &$v) {
                if ($v != null && !$v instanceof Html) { // intentionally ==
                    $v = $translator->translate($v, $count, $translate_modal);
                }
            }
        }
        return $value;
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
        if($this->hasErrors()){
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
			$validator = Validation::createValidator();
			$errors = $validator->validate($this->getValue(), $constraint);
			/** @var ConstraintViolation $error */
			foreach ($errors as $error) {

				$params = [];
				$parameters = $error->getParameters();
				if ($parameters && is_array($parameters)){
					foreach ($parameters as $key => $value) {
						$key = str_replace(["{{", " ", "}}"], "", $key);
						$params[$key] = $value;
					}
				}
				$message = $this->translate($error->getMessageTemplate(), $params);
				$this->addError($message, false);
			}
		}
	}

}
