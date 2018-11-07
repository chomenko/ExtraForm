<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 02.06.2018 23:11
 */

namespace Chomenko\ExtraForm\Controls\Traits;

use Nette\Forms\Helpers;
use Nette\Utils\Html;
use Chomenko\ExtraForm\Builds\HtmlUtility;

trait Check
{

    /**
     * @return Html
     */
    protected function getInput(){
        $this->setOption('rendered', true);
        $el = clone $this->control;
        return $el->addAttributes([
            'name' => $this->getHtmlName(),
            'disabled' => $this->isDisabled(),
            'data-nette-rules' => Helpers::exportRules($this->getRules()) ?: null,
        ]);
    }

    /**
     * @param string|integer $key
     * @return bool
     */
    public function isCheckItem($key)
    {
        $value = $this->getValue();
        if($key == $value || (is_array($value) && array_search($key, $value) !== false)){
            return true;
        }
        return false;
    }


    /**
     * @return \Nette\Utils\Html
     */
    public function getControl()
    {
        $input = $this->getInput();
        HtmlUtility::addClass($input, 'form-check-input');
        $items = Html::el();
        foreach ($this->getItems() as $value => $label_text){

            $item = clone $input;
            $this->setErrorClass($item);
            $item->addAttributes(array(
                    'value' => $value,
                    'checked' => $this->isCheckItem($value)
                )
            );

            $label = Html::el('label', array(
                'class' => 'form-check-label',
            ));
            HtmlUtility::addClass($label, 'form-check-label');
            $label->addHtml($item);
            $label->addHtml($this->translate($label_text));

            $wrapped = Html::el('div');
            HtmlUtility::addClass($wrapped, array(
                'form-check'
            ));

            //$wrapped->addHtml($item);
            $wrapped->addHtml($label);
            $items->addHtml($wrapped);
        }

        return $items;
    }


}