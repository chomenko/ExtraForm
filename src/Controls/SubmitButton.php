<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 01.06.2018 18:34
 */

namespace Chomenko\ExtraForm\Controls;

use Chomenko\ExtraForm\Events\Listener;

class SubmitButton extends \Nette\Forms\Controls\SubmitButton implements FormElement
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
