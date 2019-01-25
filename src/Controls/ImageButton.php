<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\ExtraForm\Controls;

use Chomenko\ExtraForm\Events\Listener;

class ImageButton extends \Nette\Forms\Controls\ImageButton implements FormElement
{

	use Traits\Extend;

	/**
	 * @param null $src
	 * @param null $alt
	 */
	public function __construct($src = NULL, $alt = NULL)
	{
		$this->evenListener = new Listener();
		parent::__construct($src, $alt);
	}

}
