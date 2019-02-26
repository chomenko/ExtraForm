<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\ExtraForm\Controls;

use Chomenko\ExtraForm\Events\Listener;
use Chomenko\ExtraForm\ExtraForm;
use Chomenko\ExtraForm\Controls\Traits\Button;

class LinkButton extends \Nette\Forms\Controls\BaseControl implements FormElement
{

	use Traits\Extend;
	use Button;

	/**
	 * @var string|array
	 */
	private $destination;

	/**
	 * @param string|array $destination
	 * @param string|null $caption
	 */
	public function __construct($destination, $caption = NULL)
	{
		$this->destination = $destination;
		$this->evenListener = new Listener();

		$this->evenListener->create(ControlEvents::ATTACHED, function (LinkButton $linkButton, ExtraForm $extraForm) {
			$extraForm->onAnchor[] = [$this, 'eventAttach'];
		});
		parent::__construct($caption);
	}

	/**
	 * @param ExtraForm $extraForm
	 * @throws \Nette\Application\UI\InvalidLinkException
	 */
	public function eventAttach(ExtraForm $extraForm)
	{
		$params = [];
		$link = $this->getDestination();
		if (is_array($link)) {
			[$link, $params] = $link;
		}

		if (!filter_var($link, FILTER_VALIDATE_URL)) {
			$link = $extraForm->getPresenter()->link($link, $params);
		}
		$this->setAttribute('href', $link);
	}

	/**
	 * @return string|array
	 */
	public function getDestination()
	{
		return $this->destination;
	}

}
