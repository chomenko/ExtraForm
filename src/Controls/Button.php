<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\ExtraForm\Controls;

use Chomenko\ExtraForm\Events\Listener;

class Button extends \Nette\Forms\Controls\Button implements FormElement
{

	use Traits\Extend;
	use Traits\Button;

	/**
	 * @param null $caption
	 */
	public function __construct($caption = NULL)
	{
		$this->evenListener = new Listener();
		parent::__construct($caption);
	}

}
