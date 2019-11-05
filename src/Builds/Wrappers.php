<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\ExtraForm\Builds;

use Chomenko\ExtraForm\Controls\LinkButton;
use Chomenko\ExtraForm\Controls\Recaptcha;
use Nette\Forms\Controls\Button;
use Nette\Forms\Controls\Checkbox;
use Nette\Forms\Controls\CheckboxList;
use Nette\Forms\Controls\HiddenField;
use Nette\Forms\Controls\ImageButton;
use Nette\Forms\Controls\MultiSelectBox;
use Nette\Forms\Controls\RadioList;
use Nette\Forms\Controls\SelectBox;
use Nette\Forms\Controls\SubmitButton;
use Nette\Forms\Controls\TextArea;
use Nette\Forms\Controls\TextInput;
use Nette\Forms\Controls\UploadControl;
use Nette\Utils\Html;

trait Wrappers
{

	/**
	 * @param TextInput $component
	 * @return Html
	 */
	protected function renderTextInput(TextInput $component)
	{
		$wrapper = Html::el("div", [
			'class' => 'form-group',
		]);
		$wrapper->addHtml($component->getLabel());
		$wrapper->addHtml($component->getControl());
		return $wrapper;
	}

	/**
	 * @param Recaptcha $component
	 * @return Html
	 */
	protected function renderRecaptcha(Recaptcha $component)
	{
		$wrapper = Html::el("div");
		$wrapper->addHtml($component->getControl());
		return $wrapper;
	}

	/**
	 * @param TextArea $component
	 * @return Html
	 */
	protected function renderTextArea(TextArea $component)
	{
		$wrapper = Html::el("div", [
			'class' => 'form-group',
		]);
		$wrapper->addHtml($component->getLabel());
		$wrapper->addHtml($component->getControl());
		return $wrapper;
	}

	/**
	 * @param SelectBox $component
	 * @return Html
	 */
	protected function renderSelectBox(SelectBox $component)
	{
		$wrapper = Html::el("div", [
			'class' => 'form-group',
		]);
		$wrapper->addHtml($component->getLabel());
		$wrapper->addHtml($component->getControl());
		return $wrapper;
	}

	/**
	 * @param MultiSelectBox $component
	 * @return Html
	 */
	protected function renderMultiSelectBox(MultiSelectBox $component)
	{
		$wrapper = Html::el("div", [
			'class' => 'form-group',
		]);
		$wrapper->addHtml($component->getLabel());
		$wrapper->addHtml($component->getControl());
		return $wrapper;
	}

	/**
	 * @param UploadControl $component
	 * @return Html
	 */
	protected function renderUploadControl(UploadControl $component)
	{
		$wrapper = Html::el("div", [
			'class' => 'form-group',
		]);
		$wrapper->addHtml($component->getLabel());
		$wrapper->addHtml($component->getControl());
		return $wrapper;
	}

	/**
	 * @param ImageButton $component
	 * @return Html
	 */
	protected function renderImageButton(ImageButton $component)
	{
		$wrapper = Html::el("div", [
			'class' => 'form-group',
		]);
		$wrapper->addHtml($component->getControl());
		return $wrapper;
	}

	/**
	 * @param Checkbox $component
	 * @return Html
	 */
	protected function renderCheckbox(Checkbox $component)
	{
		$wrapper = Html::el("div", [
			'class' => 'form-check',
		]);
		$wrapper->addHtml($component->getControl());
		return Html::el('div', ['class' => 'form-group'])->setHtml($wrapper);
	}

	/**
	 * @param SubmitButton $component
	 * @return Html
	 */
	protected function renderSubmitButton(SubmitButton $component)
	{
		$wrapper = Html::el("div", [
			'class' => 'form-group',
		]);
		$wrapper->addHtml($component->getControl());
		return $wrapper;
	}

	/**
	 * @param Button $component
	 * @return Html
	 */
	protected function renderButton(Button $component)
	{
		$wrapper = Html::el("div", [
			'class' => 'form-group',
		]);
		$wrapper->addHtml($component->getControl());
		return $wrapper;
	}

	/**
	 * @param HiddenField $component
	 * @return Html
	 */
	protected function renderHiddenField(HiddenField $component)
	{
		return $component->getControl();
	}

	/**
	 * @param CheckboxList $component
	 * @return Html
	 */
	protected function renderCheckboxList(CheckboxList $component)
	{
		$wrapper = Html::el("div", [
			'class' => 'form-group',
		]);

		if ($label = $component->getLabel()) {
			$wrapper->addHtml($component->getLabel());
		}
		$wrapper->addHtml($component->getControl());
		return $wrapper;
	}

	/**
	 * @param RadioList $component
	 * @return Html
	 */
	protected function renderRadioList(RadioList $component)
	{
		$wrapper = Html::el("div", [
			'class' => 'form-group',
		]);
		if ($label = $component->getLabel()) {
			$wrapper->addHtml($component->getLabel());
		}
		$wrapper->addHtml($component->getControl());
		return $wrapper;
	}

	/**
	 * @param Button $component
	 * @return Html
	 */
	protected function renderLinkButton(LinkButton $component)
	{
		$wrapper = Html::el("div", [
			'class' => 'form-group',
		]);
		$wrapper->addHtml($component->getControl());
		return $wrapper;
	}

}
