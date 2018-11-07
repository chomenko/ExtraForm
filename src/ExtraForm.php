<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 01.06.2018 17:19
 */

namespace Chomenko\ExtraForm;

use Nette;
use Nette\Application\UI\Form;

/**
 * Class ExtraForm
 * @property-read Render $renderer
 * @package Chomenko\ExtraForm
 */
class ExtraForm extends Form
{

    /**
     * @var Builder
     */
    protected $builder;
    

    public function __construct(Nette\ComponentModel\IContainer $parent = null, $name = null)
    {
        parent::__construct($parent, $name);
        $render = new Render($this);
        $this->builder = $render->builder();
        $this->setRenderer($render);
    }


    /**
     * @return Builder
     */
    public function builder()
    {
        return $this->builder;
    }


    /**
     * Adds single-line text input control to the form.
     * @param  string
     * @param  string|object
     * @param  int
     * @param  int
     * @return Controls\TextInput
     */
    public function addText($name, $label = null, $cols = null, $maxLength = null)
    {
        return $this[$name] = (new Controls\TextInput($label, $maxLength))
            ->setHtmlAttribute('size', $cols);
    }


    /**
     * Adds single-line text input control used for sensitive input such as passwords.
     * @param  string
     * @param  string|object
     * @param  int
     * @param  int
     * @return Controls\TextInput
     */
    public function addPassword($name, $label = null, $cols = null, $maxLength = null)
    {
        return $this[$name] = (new Controls\TextInput($label, $maxLength))
            ->setHtmlAttribute('size', $cols)
            ->setHtmlType('password');
    }


    /**
     * Adds multi-line text input control to the form.
     * @param  string
     * @param  string|object
     * @param  int
     * @param  int
     * @return Controls\TextArea
     */
    public function addTextArea($name, $label = null, $cols = null, $rows = null)
    {
        return $this[$name] = (new Controls\TextArea($label))
            ->setHtmlAttribute('cols', $cols)->setHtmlAttribute('rows', $rows);
    }


    /**
     * Adds input for email.
     * @param  string
     * @param  string|object
     * @return Controls\TextInput
     */
    public function addEmail($name, $label = null)
    {
        return $this[$name] = (new Controls\TextInput($label))
            ->setRequired(false)
            ->addRule(Form::EMAIL);
    }


    /**
     * Adds input for integer.
     * @param  string
     * @param  string|object
     * @return Controls\TextInput
     */
    public function addInteger($name, $label = null)
    {
        return $this[$name] = (new Controls\TextInput($label))
            ->setNullable()
            ->setRequired(false)
            ->addRule(Form::INTEGER);
    }


    /**
     * Adds control that allows the user to upload files.
     * @param  string
     * @param  string|object
     * @param  bool
     * @return Controls\UploadControl
     */
    public function addUpload($name, $label = null, $multiple = false)
    {
        return $this[$name] = new Controls\UploadControl($label, $multiple);
    }


    /**
     * Adds control that allows the user to upload multiple files.
     * @param  string
     * @param  string|object
     * @return Controls\UploadControl
     */
    public function addMultiUpload($name, $label = null)
    {
        return $this[$name] = new Controls\UploadControl($label, true);
    }


    /**
     * Adds check box control to the form.
     * @param  string
     * @param  string|object
     * @return Controls\Checkbox
     */
    public function addCheckbox($name, $caption = null)
    {
        return $this[$name] = new Controls\Checkbox($caption);
    }


    /**
     * Adds set of radio button controls to the form.
     * @param  string
     * @param  string|object
     * @return Controls\RadioList
     */
    public function addRadioList($name, $label = null, array $items = null)
    {
        return $this[$name] = new Controls\RadioList($label, $items);
    }


    /**
     * Adds set of checkbox controls to the form.
     * @param  string
     * @param  string|object
     * @return Controls\CheckboxList
     */
    public function addCheckboxList($name, $label = null, array $items = null)
    {
        return $this[$name] = new Controls\CheckboxList($label, $items);
    }


    /**
     * Adds select box control that allows single item selection.
     * @param  string
     * @param  string|object
     * @param  array
     * @param  int
     * @return Controls\SelectBox
     */
    public function addSelect($name, $label = null, array $items = null, $size = null)
    {
        return $this[$name] = (new Controls\SelectBox($label, $items))
            ->setHtmlAttribute('size', $size > 1 ? (int) $size : null);
    }


    /**
     * Adds select box control that allows multiple item selection.
     * @param  string
     * @param  string|object
     * @param  array
     * @param  int
     * @return Controls\MultiSelectBox
     */
    public function addMultiSelect($name, $label = null, array $items = null, $size = null)
    {
        return $this[$name] = (new Controls\MultiSelectBox($label, $items))
            ->setHtmlAttribute('size', $size > 1 ? (int) $size : null);
    }


    /**
     * Adds button used to submit form.
     * @param  string
     * @param  string|object
     * @return Controls\SubmitButton
     */
    public function addSubmit($name, $caption = null)
    {
        $control = new Controls\SubmitButton($caption);
        $html = $control->getControlPrototype();
        $html->setName('button');
        return $this[$name] = $control;
    }


    /**
     * Adds push buttons with no default behavior.
     * @param  string
     * @param  string|object
     * @return Controls\Button
     */
    public function addButton($name, $caption = null)
    {
        $control = new Controls\Button($caption);
        $html = $control->getControlPrototype();
        $html->setName('button');
        return $this[$name] = $control;
    }


    /**
     * Adds graphical button used to submit form.
     * @param  string
     * @param  string  URI of the image
     * @param  string  alternate text for the image
     * @return Controls\ImageButton
     */
    public function addImage($name, $src = null, $alt = null)
    {
        return $this[$name] = new Controls\ImageButton($src, $alt);
    }

}