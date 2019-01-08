<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 01.06.2018 18:30
 */

namespace Chomenko\ExtraForm\Controls;

use Chomenko\ExtraForm\Builds\HtmlUtility;
use Chomenko\ExtraForm\Events\Listener;

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
        $el = $this->getLabelPart()->insert(0, $this->getControlPart());
        $this->setErrorClass($el);
        HtmlUtility::addClass($el->getChildren()[0], 'form-check-input');
        return $el;
    }

}
