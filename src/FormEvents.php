<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 05.01.2019
 */

namespace Chomenko\ExtraForm;

use Chomenko\ExtraForm\Events\IFormEvent;

class FormEvents
{

	/**
	 * @var array
	 */
	private $list = [];

	/**
	 * @param IFormEvent $event
	 */
	public function addEvent(IFormEvent $event)
	{
		$this->list[] = $event;
	}

	/**
	 * @return array
	 */
	public function getEvents(): array
	{
		return $this->list;
	}

}
