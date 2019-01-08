<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 31.05.2018 22:35
 */

namespace Chomenko\ExtraForm;

use Chomenko\ExtraForm\Builds\Grid;
use Chomenko\ExtraForm\Builds\Group;
use Chomenko\ExtraForm\Builds\Make;
use Chomenko\ExtraForm\Builds\Wrappers;
use Nette\Utils\Html;

class Builder extends Make
{

    use Wrappers;
    use Grid;

    /**
     * @param string|array $items
     * @param string $label
     * @return Group
     */
    public function addGroup($label = null, $items = null)
    {

        $wrapped = Html::el('div', array('class' => 'form-group'));
        $item_to = Html::el('div', array('class' => 'input-group'));

        if($label){
            $wrapped->addHtml(Html::el('label')->setHtml($this->render->translate($label)));
        }

        $group = new Group($this->render, $this, $wrapped, $item_to);
        $group->addItem($items);
        $this->addItem($group);
        return $group;
    }


}