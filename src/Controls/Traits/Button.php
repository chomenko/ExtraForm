<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\ExtraForm\Controls\Traits;

use Nette\Utils\Html;
use Chomenko\ExtraForm\Builds\HtmlUtility;

trait Button
{

	/**
	 * @var array
	 */
	protected $buttonStyle = 'btn-default';

	/**
	 * @var string|null
	 */
	protected $buttonSize = NULL;

	/**
	 * @var boolean
	 */
	protected $block = FALSE;

	/**
	 * @var array
	 */
	protected $beforeIcons = [];

	/**
	 * @var array
	 */
	protected $afterIcons = [];

	/**
	 * @param string $class
	 * @param bool $before
	 * @return $this
	 */
	public function addIcon(string $class, $before = TRUE)
	{
		if ($before) {
			$this->beforeIcons[] = $class;
		} else {
			$this->afterIcons[] = $class;
		}
		return $this;
	}

	/**
	 * @param bool $block
	 * @return $this
	 */
	public function setBlock(bool $block = TRUE)
	{
		$this->block = $block;
		return $this;
	}

	/**
	 * @return $this
	 */
	public function setSizeLG()
	{
		$this->buttonSize = "btn-lg";
		return $this;
	}

	/**
	 * @return $this
	 */
	public function setSizeDefault()
	{
		$this->buttonSize = NULL;
		return $this;
	}

	/**
	 * @return $this
	 */
	public function setSizeSM()
	{
		$this->buttonSize = "btn-sm";
		return $this;
	}

	/**
	 * @return $this
	 */
	public function asPrimary()
	{
		$this->buttonStyle = "btn-primary";
		return $this;
	}

	/**
	 * @return $this
	 */
	public function asSecondary()
	{
		$this->buttonStyle = "btn-secondary";
		return $this;
	}

	/**
	 * @return $this
	 */
	public function asSuccess()
	{
		$this->buttonStyle = "btn-success";
		return $this;
	}

	/**
	 * @return $this
	 */
	public function asDanger()
	{
		$this->buttonStyle = "btn-danger";
		return $this;
	}

	/**
	 * @return $this
	 */
	public function asWarning()
	{
		$this->buttonStyle = "btn-warning";
		return $this;
	}

	/**
	 * @return $this
	 */
	public function asInfo()
	{
		$this->buttonStyle = "btn-warning";
		return $this;
	}

	/**
	 * @return $this
	 */
	public function asLight()
	{
		$this->buttonStyle = "btn-light";
		return $this;
	}

	/**
	 * @return $this
	 */
	public function asDark()
	{
		$this->buttonStyle = "btn-dark";
		return $this;
	}

	/**
	 * @return $this
	 */
	public function asLink()
	{
		$this->buttonStyle = "btn-link";
		return $this;
	}


	/**
	 * @param string|object $caption
	 * @return Html
	 */
	public function getControl($caption = NULL)
	{
		$this->setOption('rendered', TRUE);
		/** @var Html $el */
		$el = clone $this->control;
		$el->addAttributes([
			'name' => $this->getHtmlName(),
			'disabled' => $this->isDisabled(),
		]);

		$class = [];
		$class[] = "btn";
		$class[] = $this->buttonStyle;
		if ($this->buttonSize) {
			$class[] = $this->buttonSize;
		}
		if ($this->block) {
			$class[] = "btn-block";
		}

		if (!$el->getAttribute("class")) {
			HtmlUtility::addClass($el, $class);
		}

		foreach ($this->beforeIcons as $attrClass) {
			$el->addHtml(Html::el('i')->setAttribute('class', $attrClass));
			$el->addHtml('&nbsp;');
		}

		$content = $this->translate($caption === NULL ? $this->caption : $caption);
		if ($content) {
			$el->addHtml($content);
		}
		foreach ($this->afterIcons as $attrClass) {
			$el->addHtml('&nbsp;');
			$el->addHtml(Html::el('i')->setAttribute('class', $attrClass));
		}
		return $el;
	}

}
