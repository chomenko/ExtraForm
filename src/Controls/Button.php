<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 01.06.2018 18:29
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
	public function __construct($caption = null)
	{
		$this->evenListener = new Listener();
		parent::__construct($caption);
	}

}
