<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
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
	 * @param array|string $item
	 * @return $this
	 * @throws \Chomenko\ExtraForm\Exception\Exception
	 */
	public function prepend($item)
	{
		$this->getPrepend()->addItem($item);
		return $this;
	}

	/**
	 * @param string $text
	 * @return $this
	 * @throws \Chomenko\ExtraForm\Exception\Exception
	 */
	public function prependText(string $text)
	{
		$this->getPrepend()->addItem(
			Html::el('span', ['class' => 'input-group-text'])
				->setHtml($this->render->translate($text))
		);
		return $this;
	}

	/**
	 * @param string $class
	 * @return $this
	 * @throws \Chomenko\ExtraForm\Exception\Exception
	 */
	public function prependIcon(string $class)
	{
		$this->getPrepend()->addItem(
			Html::el('span', ['class' => 'input-group-text'])
				->setHtml(Html::el('i', ['class' => $class]))
		);
		return $this;
	}

	/**
	 * @param string|array $item
	 * @return $this
	 * @throws \Chomenko\ExtraForm\Exception\Exception
	 */
	public function append($item)
	{
		$this->getAppend()->addItem($item);
		return $this;
	}

	/**
	 * @param string $text
	 * @return $this
	 * @throws \Chomenko\ExtraForm\Exception\Exception
	 */
	public function appendText(string $text)
	{
		$this->getAppend()->addItem(
			Html::el('span', ['class' => 'input-group-text'])
				->setHtml($this->render->translate($text))
		);
		return $this;
	}

	/**
	 * @param string $class
	 * @return $this
	 * @throws \Chomenko\ExtraForm\Exception\Exception
	 */
	public function appendIcon(string $class): Group
	{
		$this->getAppend()->addItem(
			Html::el('span', ['class' => 'input-group-text'])
				->setHtml(Html::el('i', ['class' => $class]))
		);
		return $this;
	}

	/**
	 * @return Group
	 */
	protected function getPrepend(): Group
	{
		if (!$this->prepend) {
			$wrapped = Html::el('div', ['class' => 'input-group-prepend']);
			$this->prepend = new Group($this->render, $this, $wrapped);
			$this->prependItem($this->prepend);
			return $this->prepend;
		}
		return $this->prepend;
	}

	/**
	 * @return Group
	 * @throws \Chomenko\ExtraForm\Exception\Exception
	 */
	protected function getAppend(): Group
	{
		if (!$this->append) {
			$wrapped = Html::el('div', ['class' => 'input-group-append']);
			$this->append = new Group($this->render, $this, $wrapped);
			$this->addItem($this->append);
			return $this->append;
		}
		return $this->append;
	}

}
