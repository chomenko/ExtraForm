<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 01.06.2018 18:36
 */

namespace Chomenko\ExtraForm\Controls;

use Chomenko\ExtraForm\Events\Listener;
use Nette\Utils\Html;

use Chomenko\ExtraForm\Builds\HtmlUtility;

class UploadControl extends \Nette\Forms\Controls\UploadControl implements FormElement
{
    use Traits\Extend;

    /**
     * @var boolean
     */
    protected $custom = false;

    /**
     * @var string|null|Html
     */
    protected $input_caption;

	/**
	 * @param null $label
	 * @param bool $multiple
	 */
    public function __construct($label = NULL, bool $multiple = FALSE)
	{
		$this->evenListener = new Listener();
		parent::__construct($label, $multiple);
	}

	/**
     * @param string|null|Html $caption
     * @param boolean $custom
     * @return $this
     */
    public function asCustom($caption = null, $custom = true)
    {
        $this->input_caption = $caption;
        $this->custom = $custom;
        return $this;
    }

    /**
     * @return bool
     */
    public function isCustom()
    {
        return $this->custom;
    }

    /**
     * @return Html|string
     */
    public function getControl()
    {

        $control =  parent::getControl();

        if(!$this->custom){
            return $control;
        }

        HtmlUtility::addClass($control, 'custom-file-input');
        $wrapped = Html::el('div', ['class' => ['custom-file']]);
        $this->setErrorClass($control);
        $wrapped->addHtml($control);

        $label = Html::el('label', array(
            'class' => ['custom-file-label'],
            'for'   => $this->getHtmlId(),
        ));

        if(!empty($this->input_caption)){
            $label->addHtml($this->translate($this->input_caption));
        }
        $wrapped->addHtml($label);
        return $wrapped;
    }


}