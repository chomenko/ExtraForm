<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\ExtraForm;

use Nette\Forms\Form;
use Nette\Forms\IFormRenderer;
use Nette\Utils\Html;

class Render implements IFormRenderer
{

	/**
	 * @var Builder
	 */
	protected $builder;

	/**
	 * @var Form
	 */
	protected $form;

	/**
	 * @var array
	 */
	protected $usedItems = [];

	/**
	 * @param ExtraForm $form
	 */
	public function __construct(ExtraForm $form)
	{
		$this->form = $form;
		$this->builder = new Builder($this);
		$this->builder->__setReference('#form');
	}

	/**
	 * @param string $message
	 * @param null $count
	 * @param null $modal
	 * @return string|Html
	 */
	public function translate($message, $count = NULL, $modal = NULL)
	{
		if ($translate = $this->form->getTranslator()) {
			return $translate->translate($message, $count, $this->form->getTranslateFile(), $modal);
		};
		return $message;
	}

	/**
	 * @return Builder
	 */
	public function builder(): Builder
	{
		return $this->builder;
	}

	/**
	 * @param Form $form
	 * @return string|Html
	 */
	public function render(Form $form)
	{
		$this->form = $form;

		$formHtml = $form->getElementPrototype();

		if ($form instanceof ExtraForm) {
			$form->getEventsListener()->emit(ExtraForm::BEFORE_RENDER, $formHtml, $this, $form);
		}

		$this->resort();

		$content = $this->builder->make($form);
		$errorsHtml = $this->renderErrors();

		$formHtml->addHtml($errorsHtml);
		$formHtml->addHtml($content);

		if ($form instanceof ExtraForm) {
			$form->getEventsListener()->emit(ExtraForm::AFTER_RENDER, $formHtml, $this, $form);
		}

		return $formHtml;
	}

	/**
	 * @return Html
	 */
	public function renderErrors()
	{
		$html = Html::el();
		foreach ($this->form->getErrors() as $error) {
			$htmlError = Html::el('div', [
				'class' => 'alert alert-danger',
				'role' => 'alert',
			]);
			$htmlError->addHtml($error);
			$html->addHtml($htmlError);
		}
		return $html;
	}

	protected function resort()
	{
		$sorted = [];
		foreach ($this->form->getComponents() as $name => $component) {
			if (!$this->isUsed($name)) {
				$sorted[] = $name;
			} elseif ($item = $this->builder->__getByReference($name)) {
				$sorted[] = $item;
			}
		}
		$this->builder->setChild($sorted);
	}

	/**
	 * @param mixed $components
	 */
	protected function renderComponents($components)
	{
		/** @var \Nette\Forms\Controls\BaseControl $component */
		foreach ((array)$components as $component) {

			$path = explode('\\', get_class($component));
			$name = 'render' . array_pop($path);
			if (method_exists($this, $name)) {
				$html = $this->{'render' . $name}($components);
			}
		}
	}

	/**
	 * @internal
	 * @param string $name
	 */
	public function useItem(string $name)
	{
		$this->usedItems[$name] = $name;
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	protected function isUsed(string $name): bool
	{
		if (array_key_exists($name, $this->usedItems)) {
			return TRUE;
		}
		return FALSE;
	}

}
