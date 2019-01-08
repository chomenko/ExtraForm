<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 01.06.2018 18:35
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
        $class = array();
        $class[] = 'form-control';

        if($this->input_size){
            $class[] = $this->input_size;
        }

        HtmlUtility::addClass($el, $class);
        return $el;
    }

}
