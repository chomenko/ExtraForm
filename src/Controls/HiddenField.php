<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 01.06.2018 18:35
 */

namespace Chomenko\ExtraForm\Controls;

use Chomenko\ExtraForm\Events\Listener;

class HiddenField extends \Nette\Forms\Controls\HiddenField implements FormElement
{

    use Traits\Extend;

	/**
	 * @param null $persistentValue
	 */
	public function __construct($persistentValue = NULL)
	{
		$this->evenListener = new Listener();
		parent::__construct($persistentValue);
	}

}
