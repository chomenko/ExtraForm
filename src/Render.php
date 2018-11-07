<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 31.05.2018 21:59
 */

namespace Chomenko\ExtraForm;


use Nette\Forms\Form;
use Nette\Forms\IFormRenderer;
use Nette\Utils\Html;

class Render implements IFormRenderer
{
    /**
     * @var Builder
     */
    protected $builder;

    /**
     * @var Form
     */
    protected $form;

    /**
     * @var array
     */
    protected $used_items = array();


    public function __construct(Form $form)
    {
        $this->form = $form;
        $this->builder = new Builder($this);
        $this->builder->__setReference('#form');
    }


    /**
     * @param string $message
     * @param null $count
     * @param null $modal
     * @return string|Html
     */
    public function translate($message, $count = null, $modal = null){
        if($translate = $this->form->getTranslator()){
            return $translate->translate($message,$count,$modal);
        };
        return $message;
    }



    /**
     * @return Builder
     */
    public function builder(){
        return $this->builder;
    }


    /**
     * @param Form $form
     * @return string|void
     */
    public function render(Form $form)
    {
        $this->form = $form;
        $form_html = $form->getElementPrototype();

        $this->resort();

        $content = $this->builder->make($form);
        $errors_html = $this->renderErrors();

        $form_html->addHtml($errors_html);
        $form_html->addHtml($content);

        return $form_html;
    }

    /**
     * @return Html
     */
    public function renderErrors()
    {
        $html = Html::el();
        foreach ($this->form->getErrors() as $error){
            $html_error = Html::el('div', array(
                'class' => 'alert alert-danger',
                'role' => 'alert'
            ));
            $html_error->addHtml($error);
            $html->addHtml($html_error);
        }
        return $html;
    }


    protected function resort(){
        $sorted = array();
        foreach($this->form->getComponents() as $name => $component){
            if(!$this->isUsed($name)){
                $sorted[] = $name;
            }elseif($item = $this->builder->__getByReference($name)){
                $sorted[] = $item;
            }
        }
        $this->builder->setChild($sorted);
    }


    /**
     * @param $components
     */
    protected function renderComponents($components)
    {
        /** @var \Nette\Forms\Controls\BaseControl $component */
        foreach((array) $components as $component){

            $path = explode('\\', get_class($component));
            $name = 'render'.array_pop($path);
            if (method_exists($this, $name)) {
                $html = $this->{'render' . $name}($components);
            }
        }
    }

    /**
     * @internal
     * @param $name
     */
    public function useItem($name){
        $this->used_items[$name] = $name;
    }

    /**
     * @param $name
     * @return bool
     */
    protected function isUsed($name){
        if(array_key_exists($name, $this->used_items)){
            return true;
        }
        return false;
    }

}