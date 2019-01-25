<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\ExtraForm\Controls;

use Chomenko\ExtraForm\Events\Listener;

class RadioList extends \Nette\Forms\Controls\RadioList implements FormElement
{

	use Traits\Extend;
	use Traits\Check;
	use Traits\Choice;

	/**
	 * @param null $label
	 * @param array|NULL $items
	 */
	public function __construct($label = NULL, array $items = NULL)
	{
		$this->evenListener = new Listener();
		parent::__construct($label, $items);
	}

}
