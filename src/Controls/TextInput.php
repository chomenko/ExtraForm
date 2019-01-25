<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\ExtraForm\Controls;

use Chomenko\ExtraForm\Builds\HtmlUtility;
use Chomenko\ExtraForm\Events\Listener;

class TextInput extends \Nette\Forms\Controls\TextInput implements FormElement
{

	use Traits\Extend;
	use Traits\SizeInputs;

	/**
	 * @param null $label
	 * @param null $maxLength
	 */
	public function __construct($label = NULL, $maxLength = NULL)
	{
		$this->evenListener = new Listener();
		parent::__construct($label, $maxLength);
	}

	/**
	 * @return \Nette\Utils\Html
	 */
	public function getControl()
	{
		$el = parent::getControl();
		$this->setErrorClass($el);
		$class = [];
		$class[] = 'form-control';

		if ($this->inputSize) {
			$class[] = $this->inputSize;
		}

		HtmlUtility::addClass($el, $class);
		return $el;
	}

}
