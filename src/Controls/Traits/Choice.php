<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\ExtraForm\Controls\Traits;

use Chomenko\ExtraForm\Controls\ControlEvents;

trait Choice
{

	/**
	 * @param array|object $items
	 * @param bool $useKeys
	 * @return static
	 */
	public function setItems($items = [], $useKeys = TRUE)
	{
		$this->evenListener->emit(ControlEvents::SET_ITEMS, $this, $items);
		return parent::setItems($items, $useKeys);
	}

	/**
	 * @return mixed
	 */
	public function getOriginItems()
	{
		return $this->originItems;
	}

}
