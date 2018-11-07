<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 01.06.2018 18:32
 */

namespace Chomenko\ExtraForm\Controls;

use Chomenko\ExtraForm\Builds\HtmlUtility;

class MultiSelectBox extends \Nette\Forms\Controls\MultiSelectBox
{

    use Traits\Extend;
    use Traits\SizeInputs;

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