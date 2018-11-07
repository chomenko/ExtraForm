<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 04.06.2018 18:02
 */

namespace Chomenko\ExtraForm\Builds;

use Nette\Utils\Html;

class Group extends Make
{

    /**
     * @var Group
     */
    private $prepend;

    /**
     * @var Group
     */
    private $append;

    /**
     * @param string $item
     * @return $this
     */
    public function prepend($item)
    {
        $this->getPrepend()->addItem($item);
        return $this;
    }

    /**
     * @param string $text
     * @return $this
     */
    public function prependText($text)
    {
        $this->getPrepend()->addItem(
            Html::el('span',array('class' => 'input-group-text'))
                ->setHtml($this->render->translate($text))
        );
        return $this;
    }

    /**
     * @param string $class
     * @return $this
     */
    public function prependIcon($class)
    {
        $this->getPrepend()->addItem(
            Html::el('span',array('class' => 'input-group-text'))
                ->setHtml(Html::el('i', array('class' => $class)))
        );
        return $this;
    }

    /**
     * @param string $item
     * @return $this
     */
    public function append($item)
    {
        $this->getAppend()->addItem($item);
        return $this;
    }

    /**
     * @param string $text
     * @return $this
     */
    public function appendText($text)
    {
        $this->getAppend()->addItem(
            Html::el('span',array('class' => 'input-group-text'))
                ->setHtml($this->render->translate($text))
        );
        return $this;
    }

    /**
     * @param string $class
     * @return $this
     */
    public function appendIcon($class)
    {
        $this->getAppend()->addItem(
            Html::el('span',array('class' => 'input-group-text'))
                ->setHtml(Html::el('i', array('class' => $class)))
        );
        return $this;
    }

    /**
     * @return Group
     */
    protected function getPrepend()
    {
        if(!$this->prepend){
            $wrapped = Html::el('div', array('class' => 'input-group-prepend'));
            $this->prepend = new Group($this->render, $this, $wrapped);
            $this->prependItem($this->prepend);
            return $this->prepend ;
        }
        return $this->prepend;
    }

    /**
     * @return Group
     */
    protected function getAppend()
    {
        if(!$this->append){
            $wrapped = Html::el('div', array('class' => 'input-group-append'));
            $this->append = new Group($this->render, $this, $wrapped);
            $this->addItem($this->append);
            return $this->append ;
        }
        return $this->append;
    }


}