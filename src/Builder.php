<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\ExtraForm;

use Chomenko\ExtraForm\Builds\Grid;
use Chomenko\ExtraForm\Builds\Group;
use Chomenko\ExtraForm\Builds\Make;
use Chomenko\ExtraForm\Builds\Wrappers;
use Nette\Utils\Html;

class Builder extends Make
{

	use Wrappers;
	use Grid;

	/**
	 * @param string|null $label
	 * @param array|null $items
	 * @return Group
	 */
	public function addGroup($label = NULL, $items = NULL): Group
	{
		$wrapped = Html::el('div', ['class' => 'form-group']);
		$itemTo = Html::el('div', ['class' => 'input-group']);

		if ($label) {
			$wrapped->addHtml(Html::el('label')->setHtml($this->render->translate($label)));
		}

		$group = new Group($this->render, $this, $wrapped, $itemTo);
		$group->addItem($items);
		$this->addItem($group);
		return $group;
	}

}
