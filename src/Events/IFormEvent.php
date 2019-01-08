<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 05.01.2019
 */

namespace Chomenko\ExtraForm\Events;

use Chomenko\ExtraForm\ExtraForm;

interface IFormEvent
{

	/**
	 * @param ExtraForm $form
	 * @param Listener $listener
	 * @return mixed
	 */
	public function install(ExtraForm $form, Listener $listener);


}
