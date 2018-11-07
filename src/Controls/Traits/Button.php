<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 01.06.2018 19:22
 */

namespace Chomenko\ExtraForm\Controls\Traits;


use Nette\Utils\Html;
use Chomenko\ExtraForm\Builds\HtmlUtility;

trait Button
{

    /**
     * @var array
     */
    protected $button_style = 'btn-default';

    /**
     * @var string|null
     */
    protected $button_size = null;

    /**
     * @var boolean
     */
    protected $block = false;

    /**
     * @var array
     */
    protected $before_icons = array();

    /**
     * @var array
     */
    protected $after_icons = array();

    /**
     * @param $class
     * @param bool $before
     * @return $this
     */
    public function addIcon($class, $before = true)
    {
        if($before){
            $this->before_icons[] = $class;
        }else{
            $this->after_icons[] = $class;
        }
        return $this;
    }

    /**
     * @var boolean $block
     * @return $this
     */
    public function setBlock($block = true)
    {
        $this->block = $block;
        return $this;
    }

    /**
     * @return $this
     */
    public function setSizeLG()
    {
        $this->button_size = "btn-lg";
        return $this;
    }

    /**
     * @return $this
     */
    public function setSizeDefault()
    {
        $this->button_size = null;
        return $this;
    }

    /**
     * @return $this
     */
    public function setSizeSM()
    {
        $this->button_size = "btn-sm";
        return $this;
    }

    /**
     * @return $this
     */
    public function asPrimary()
    {
        $this->button_style = "btn-primary";
        return $this;
    }

    /**
     * @return $this
     */
    public function asSecondary()
    {
        $this->button_style = "btn-secondary";
        return $this;
    }

    /**
     * @return $this
     */
    public function asSuccess()
    {
        $this->button_style = "btn-success";
        return $this;
    }

    /**
     * @return $this
     */
    public function asDanger()
    {
        $this->button_style = "btn-danger";
        return $this;
    }

    /**
     * @return $this
     */
    public function asWarning()
    {
        $this->button_style = "btn-warning";
        return $this;
    }

    /**
     * @return $this
     */
    public function asInfo()
    {
        $this->button_style = "btn-warning";
        return $this;
    }

    /**
     * @return $this
     */
    public function asLight()
    {
        $this->button_style = "btn-light";
        return $this;
    }

    /**
     * @return $this
     */
    public function asDark()
    {
        $this->button_style = "btn-dark";
        return $this;
    }

    /**
     * @return $this
     */
    public function asLink()
    {
        $this->button_style = "btn-link";
        return $this;
    }


    /**
     * @param  string|object
     * @return Html
     */
    public function getControl($caption = null)
    {
        $this->setOption('rendered', true);
        /** @var Html $el */
        $el = clone $this->control;
        $el->addAttributes([
            'name' => $this->getHtmlName(),
            'disabled' => $this->isDisabled()
        ]);

        $class = array();
        $class[] = "btn";
        $class[] = $this->button_style;
        if($this->button_size){
            $class[] = $this->button_size;
        }
        if($this->block){
            $class[] = "btn-block";
        }

        HtmlUtility::addClass($el, $class);

        foreach ($this->before_icons as $attr_class){
            $el->addHtml(Html::el('i')->setAttribute('class', $attr_class));
            $el->addHtml('&nbsp;');
        }
        $el->addHtml($this->translate($caption === null ? $this->caption : $caption));
        foreach ($this->after_icons as $attr_class){
            $el->addHtml('&nbsp;');
            $el->addHtml(Html::el('i')->setAttribute('class', $attr_class));
        }
        return $el;
    }

}