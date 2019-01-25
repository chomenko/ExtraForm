<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\ExtraForm\Builds;

use Chomenko\ExtraForm\Builder;
use Chomenko\ExtraForm\Exception\Exception;
use Chomenko\ExtraForm\Render;
use Nette\Forms\Form;
use Nette\Utils\Html;

abstract class Make
{

	/**
	 * @var Render
	 */
	protected $render;

	/**
	 * @var Make|null
	 */
	protected $paren;

	/**
	 * @var Builder[]
	 */
	protected $child = [];

	/**
	 * @var Html|null
	 */
	protected $wrapped;

	/**
	 * @var Html|null
	 */
	protected $itemWrapped;

	/**
	 * @var string
	 */
	protected $reference;

	/**
	 * @var callable[]
	 */
	protected $beforeMake = [];

	/**
	 * @var callable[]
	 */
	protected $afterMake = [];

	/**
	 * Builder constructor.
	 * @param Render $render
	 * @param Make|null $parent
	 * @param Html $wrapped
	 * @param Html $itemWrapped
	 */
	public function __construct(Render $render, Make $parent = NULL, Html $wrapped = NULL, Html $itemWrapped = NULL)
	{
		$this->render = $render;
		$this->paren = $parent;
		$this->wrapped = $wrapped;
		$this->itemWrapped = $itemWrapped;
	}

	/**
	 * @param Form $form
	 * @param string $name
	 * @return Html
	 */
	public function getContentByItemName(Form $form, $name)
	{
		$component = $form->getComponent($name);
		$path = explode('\\', get_class($component));
		$name = array_pop($path);
		if (method_exists($this, 'render' . $name)) {
			return $this->{'render' . $name}($component);
		}
		return $component->getControl();
	}

	/**
	 * @param Form $form
	 * @return Html
	 * @internal
	 */
	public function make(Form $form)
	{
		$wrapped = $this->wrapped ? $this->wrapped : Html::el();
		$itemWrapped = $this->itemWrapped ? $this->itemWrapped : $wrapped;

		foreach ($this->beforeMake as $callable) {
			call_user_func_array($callable, [$wrapped, $itemWrapped, $this, $form]);
		}

		foreach ($this->child as $item) {
			if ($item instanceof Make) {
				$itemWrapped->addHtml($item->make($form));
			} elseif ($item instanceof Html) {
				$itemWrapped->addHtml($item);
			} else {
				if ($html = $this->getContentByItemName($form, $item)) {
					$itemWrapped->addHtml($html);
				}
			}
		}

		foreach ($this->afterMake as $callable) {
			call_user_func_array($callable, [$wrapped, $itemWrapped, $this, $form]);
		}

		return $this->itemWrapped ? $wrapped->addHtml($itemWrapped) : $itemWrapped;
	}

	/**
	 * @param string|array|Make|Html $name
	 * @throws Exception
	 */
	public function addItem($name)
	{
		if (empty($name)) {
			return;
		}

		if (!is_string($name) && !is_array($name) && !$name instanceof Make && !$name instanceof Html) {
			throw Exception::AddItemFailed();
		}

		if (is_array($name)) {
			$this->__setReference(array_values($name)[0]);
			foreach ($name as $item) {
				$this->render->useItem($item);
				$this->child[] = $item;
			}
		} else {
			$this->__setReference($name);
			if (is_string($name)) {
				$this->render->useItem($name);
			}
			$this->child[] = $name;
		}
	}

	/**
	 * @param mixed $item
	 */
	protected function prependItem($item)
	{
		$newSort = [];
		$newSort[] = $item;
		foreach ($this->child as $name => $value) {
			if (is_numeric($name)) {
				$newSort[] = $value;
			} else {
				$newSort[$name] = $value;
			}
		}
		$this->child = $newSort;
	}

	/**
	 * @param string $class
	 * @return $this
	 */
	public function addClass(string $class)
	{
		if ($this->wrapped) {
			HtmlUtility::addClass($this->wrapped, $class);
		}
		return $this;
	}

	/**
	 * @param Html $html
	 * @return $this
	 */
	public function prependHtml(Html $html)
	{
		if ($this->wrapped) {
			HtmlUtility::prependHtml($this->wrapped, $html);
		}
		return $this;
	}

	/**
	 * @param Html $html
	 * @return $this
	 */
	public function appendHtml(Html $html)
	{
		$this->afterMake[] = function ($wrapped) use ($html) {
			if ($wrapped) {
				$wrapped->addHtml($html);
			}
		};
		return $this;
	}

	/**
	 * @internal
	 * @return Builder[]
	 */
	public function getChild()
	{
		return $this->child;
	}

	/**
	 * @internal
	 * @param array $child
	 */
	public function setChild(array $child)
	{
		$this->child = $child;
	}

	/**
	 * @internal
	 * @param string $name
	 */
	public function __setReference($name)
	{
		if ($this->paren) {
			if (!$this->reference) {
				$this->reference = $name;
			}
			$this->paren->__setReference($name);
		}
	}

	/**
	 * @internal
	 * @return string
	 */
	public function __getReference()
	{
		return $this->reference;
	}

	/**
	 * @internal
	 * @param string $name
	 * @return mixed
	 */
	public function __getByReference($name)
	{
		foreach ($this->child as $item) {
			if ($item instanceof Make) {
				if ($item->__getReference() === $name) {
					return $item;
				}
			} elseif ($item === $name) {
				return $item;
			}
		}
		return FALSE;
	}

}