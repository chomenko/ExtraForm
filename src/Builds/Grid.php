<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 04.06.2018 18:13
 */

namespace Chomenko\ExtraForm\Builds;


use Chomenko\ExtraForm\Builder;
use Nette\Utils\Html;

trait Grid
{

    /**
     * @param string|array $items
     * @return Builder
     */
    public function addRow($items = null)
    {
        $wrapped = Html::el('div', array('class' => 'row'));
        $child = new Builder($this->render, $this, $wrapped);
        $child->addItem($items);
        return $this->child[] = $child;
    }

    /**
     * @param null $size
     * @param string|array $items
     * @return Builder
     */
    public function addCol($size = null, $items = null)
    {
        $wrapped = Html::el('div', array('class' => 'col'.($size? '-'.((int)$size) : '')));
        $child = new Builder($this->render, $this, $wrapped);
        $child->addItem($items);
        return $this->child[] = $child;
    }


    /**
     * @param null $size
     * @param string|array $items
     * @return Builder
     */
    public function addColSm($size = null, $items = null)
    {
        $wrapped = Html::el('div', array('class' => 'col-sm'.($size ? '-'.((int)$size) : '') ));
        $child = new Builder($this->render, $this, $wrapped);
        $child->addItem($items);
        return $this->child[] = $child;
    }


    /**
     * @param null $size
     * @param string|array $items
     * @return Builder
     */
    public function addColMd($size, $items = null)
    {
        $wrapped = Html::el('div', array('class' => 'col-md-'.((int)$size)));
        $child = new Builder($this->render, $this, $wrapped);
        $child->addItem($items);
        return $this->child[] = $child;
    }

    /**
     * @param null $size
     * @param string|array $items
     * @return Builder
     */
    public function addColLg($size, $items = null)
    {
        $wrapped = Html::el('div', array('class' => 'col-md-'.((int)$size)));
        $child = new Builder($this->render, $this, $wrapped);
        $child->addItem($items);
        return $this->child[] = $child;
    }


}