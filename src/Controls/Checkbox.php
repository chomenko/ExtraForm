<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 01.06.2018 18:30
 */

namespace Chomenko\ExtraForm\Controls;

use Chomenko\ExtraForm\Builds\HtmlUtility;
use Chomenko\ExtraForm\Events\Listener;
use Nette\Utils\Html;

class Checkbox extends \Nette\Forms\Controls\Checkbox implements FormElement
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
		$el = $this->getLabel()->insert(0, $this->getControlPart());
		$this->setErrorClass($el);
		HtmlUtility::addClass($el->getChildren()[0], 'form-check-input');
		return $el;
	}

	/**
	 * @param null $caption
	 * @return Html|string
	 */
	public function getLabel($caption = NULL)
	{
		$label = clone $this->label;
		$label->for = $this->getHtmlId();
		$captionHtml = Html::el("span", [ "class" => "caption"]);
		$captionHtml->addHtml($this->translate($caption === NULL ? $this->caption : $caption));
		$label->addHtml($captionHtml);
		return $label;
	}

}
