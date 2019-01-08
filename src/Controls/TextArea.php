<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 01.06.2018 18:34
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
        $class = array();
        $class[] = 'form-control';
        HtmlUtility::addClass($el, $class);
        return $el;
    }

    public function validate()
	{
	}

}
