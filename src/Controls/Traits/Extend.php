<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 01.06.2018 21:43
 */

namespace Chomenko\ExtraForm\Controls\Traits;


use Chomenko\ExtraForm\Builds\HtmlUtility;
use Chomenko\ExtraForm\ExtraForm;
use Nette\Utils\Html;

trait Extend
{


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
        return $form->builder()->getContentByItemName($form, $this->getName());
    }


}