<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
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
	public function addRow($items = NULL)
	{
		$wrapped = Html::el('div', ['class' => 'row']);
		$child = new Builder($this->render, $this, $wrapped);
		$child->addItem($items);
		return $this->child[] = $child;
	}

	/**
	 * @param null $size
	 * @param string|array $items
	 * @return Builder
	 */
	public function addCol($size = NULL, $items = NULL)
	{
		$wrapped = Html::el('div', ['class' => 'col' . ($size ? '-' . ((int)$size) : '')]);
		$child = new Builder($this->render, $this, $wrapped);
		$child->addItem($items);
		return $this->child[] = $child;
	}

	/**
	 * @param null $size
	 * @param string|array $items
	 * @return Builder
	 */
	public function addColSm($size = NULL, $items = NULL)
	{
		$wrapped = Html::el('div', ['class' => 'col-sm' . ($size ? '-' . ((int)$size) : '')]);
		$child = new Builder($this->render, $this, $wrapped);
		$child->addItem($items);
		return $this->child[] = $child;
	}

	/**
	 * @param null $size
	 * @param string|array $items
	 * @return Builder
	 */
	public function addColMd($size, $items = NULL)
	{
		$wrapped = Html::el('div', ['class' => 'col-md-' . ((int)$size)]);
		$child = new Builder($this->render, $this, $wrapped);
		$child->addItem($items);
		return $this->child[] = $child;
	}

	/**
	 * @param null $size
	 * @param string|array $items
	 * @return Builder
	 */
	public function addColLg($size, $items = NULL)
	{
		$wrapped = Html::el('div', ['class' => 'col-md-' . ((int)$size)]);
		$child = new Builder($this->render, $this, $wrapped);
		$child->addItem($items);
		return $this->child[] = $child;
	}

}
