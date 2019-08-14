<?php

/**
 * Author: Radek Zíka
 * Email: radek.zika@dipcom.cz
 */

namespace Chomenko\ExtraForm\Extend;

use Chomenko\ExtraForm\EntityForm;
use Chomenko\ExtraForm\Events\IFormEvent;
use Chomenko\ExtraForm\Events\Listener;
use Chomenko\ExtraForm\ExtraForm;
use Chomenko\ExtraForm\Render;
use Nette\Utils\Html;

class ChangeSet implements IFormEvent
{

	/**
	 * @param ExtraForm $form
	 * @param Listener $listener
	 */
	public function install(ExtraForm $form, Listener $listener)
	{
		if ($form instanceof EntityForm) {
			$listener->create(EntityForm::BEFORE_RENDER, [$this, "compareChangeSet"]);
		}
	}

	public function compareChangeSet(Html $formHtml, Render $render, EntityForm $form)
	{
		$form->applyEntityChange();
	}

}
