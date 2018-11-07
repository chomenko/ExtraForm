<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 01.06.2018 18:34
 */

namespace Chomenko\ExtraForm\Controls;

use Chomenko\ExtraForm\Builds\HtmlUtility;

class TextArea extends \Nette\Forms\Controls\TextArea
{
    use Traits\Extend;

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
}