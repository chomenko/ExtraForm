<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\ExtraForm;

use Nette\Localization\ITranslator;
use Nette\Utils\Html;

class TranslatorWrapped implements ITranslator
{

	/**
	 * @var ExtraForm
	 */
	protected $form;

	/**
	 * @var ITranslator|null
	 */
	protected $translator;

	/**
	 * @var string|null
	 */
	protected $translateFile;

	/**
	 * @param ExtraForm $form
	 */
	public function __construct(ExtraForm $form)
	{
		$this->form = $form;
	}

	/**
	 * @param ITranslator|null $translator
	 */
	public function setTranslator(ITranslator $translator = NULL)
	{
		$this->translator = $translator;
	}

	/**
	 * @return ITranslator|null
	 */
	public function getTranslator()
	{
		return $this->translator;
	}

	/**
	 * @param string $message
	 * @param null $count
	 * @return Html|string
	 */
	public function translate($message, $count = NULL)
	{
		if ($this->translator) {
			return call_user_func_array([$this->translator, "translate"], [$message, $count, $this->translateFile]);
		}
		return $message;
	}

	/**
	 * @return null|string
	 */
	public function getTranslateFile(): ?string
	{
		return $this->translateFile;
	}

	/**
	 * @param null|string $translateFile
	 */
	public function setTranslateFile($translateFile)
	{
		$this->translateFile = $translateFile;
	}

}

