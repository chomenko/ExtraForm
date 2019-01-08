<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 01.06.2018 18:32
 */

namespace Chomenko\ExtraForm\Controls;

use Chomenko\ExtraForm\Builds\HtmlUtility;
use Chomenko\ExtraForm\Events\Listener;

class MultiSelectBox extends \Nette\Forms\Controls\MultiSelectBox implements FormElement
{

    use Traits\Extend;
    use Traits\SizeInputs;
	use Traits\Choice;

	/**
	 * @param null $label
	 * @param array|NULL $items
	 */
	public function __construct($label = NULL, array $items = NULL)
	{
		$this->evenListener = new Listener();
		parent::__construct($label, $items);
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
