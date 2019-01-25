<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\ExtraForm\Controls;

use Chomenko\ExtraForm\Builds\HtmlUtility;
use Chomenko\ExtraForm\Events\Listener;

class TextArea extends \Nette\Forms\Controls\TextArea implements FormElement
{

	use Traits\Extend;

	/**
	 * @param null $label
	 */
	public function __construct($label = NULL)
	{
		$this->evenListener = new Listener();
		parent::__construct($label);
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
		HtmlUtility::addClass($el, $class);
		return $el;
	}

	public function validate()
	{
	}

}
