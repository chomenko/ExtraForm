<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\ExtraForm\Controls\Traits;

use Nette\Forms\Helpers;
use Nette\Utils\Html;
use Chomenko\ExtraForm\Builds\HtmlUtility;

trait Check
{

	/**
	 * @return Html
	 */
	protected function getInput()
	{
		$this->setOption('rendered', TRUE);
		$el = clone $this->control;
		return $el->addAttributes([
			'name' => $this->getHtmlName(),
			'disabled' => $this->isDisabled(),
			'data-nette-rules' => Helpers::exportRules($this->getRules()) ? : NULL,
		]);
	}

	/**
	 * @param string|integer $key
	 * @return bool
	 */
	public function isCheckItem($key): bool
	{
		$value = $this->getValue();
		if ($key == $value || (is_array($value) && array_search($key, $value) !== FALSE)) {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * @return \Nette\Utils\Html
	 */
	public function getControl()
	{
		$input = $this->getInput();
		HtmlUtility::addClass($input, 'form-check-input');
		$items = Html::el();
		$id = $input->getAttribute("id");

		foreach ($this->getItems() as $value => $labelText) {
			$item = clone $input;
			$this->setErrorClass($item);
			$item->addAttributes([
				'value' => $value,
				'checked' => $this->isCheckItem($value),
				'id' => $id . "-" . $value,
			]);

			$label = Html::el('label', [
				'class' => 'form-check-label',
			]);
			HtmlUtility::addClass($label, 'form-check-label');
			$label->addHtml($item);

			$span = Html::el("span", ["class" => "caption"])
				->setHtml($this->translate($labelText));
			$label->addHtml($span);

			$wrapped = Html::el('div');
			HtmlUtility::addClass($wrapped, [
				'form-check'
			]);
			$wrapped->addHtml($label);
			$items->addHtml($wrapped);
		}

		return $items;
	}

}
