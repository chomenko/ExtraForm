<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\ExtraForm\Controls;

use Chomenko\ExtraForm\Builds\HtmlUtility;
use Chomenko\ExtraForm\Events\Listener;
use Nette\ComponentModel\IContainer;
use Nette\Http\Url;
use Nette\Utils\Html;

class Recaptcha extends \Nette\Forms\Controls\TextInput implements FormElement
{

	use Traits\Extend;
	use Traits\SizeInputs;

	/**
	 * @var string
	 */
	private $domainKey;

	/**
	 * @var string
	 */
	private $secretKey;

	/**
	 * @var int
	 */
	private $tryCount;

	/**
	 * @var bool
	 */
	private $enable;

	/**
	 * @var \Nette\Http\Session|\Nette\Http\SessionSection
	 */
	private $session;

	/**
	 * Recaptcha constructor.
	 * @param string $domainKey
	 * @param $secretKey
	 * @param $tryCount
	 * @param bool $enable
	 */
	public function __construct(string $domainKey, $secretKey, $tryCount, $enable = TRUE)
	{
		$this->evenListener = new Listener();
		$this->domainKey = $domainKey;
		$this->secretKey = $secretKey;
		$this->tryCount = $tryCount;
		$this->enable = $enable;
		parent::__construct(NULL, NULL);
	}

	public function setParent(IContainer $parent = null, $name = null)
	{
		parent::setParent($parent, $name);
		$this->getForm()->onAnchor[] = [$this, "onAnchor"];
		$this->getForm()->onError[] = [$this, "onError"];
	}

	public function onAnchor() {
		$this->session = $session = $this->getForm()->getPresenter()->getSession($this->getForm()->getName());
		if (!$this->session->tryCount) {
			$this->session->tryCount = 0;
		}
	}

	public function onError()
	{
		if ($this->getForm()->hasOnlyValidation()){
			return;
		}
		$this->session->tryCount++;
	}

	/**
	 * @return \Nette\Utils\Html
	 */
	public function getControl()
	{

		$cool= Html::el("div");
		if ($this->enable === FALSE || $this->enable && $this->session->tryCount < $this->tryCount &&  $this->tryCount !== 0) {
			return $cool;
		}

		$script = Html::el("script", [
			"src" => "https://www.google.com/recaptcha/api.js",
			"async", "defer",
		]);

		$captch = Html::el("div", [
			"class" => "g-recaptcha",
			"data-size" => "normal",
			"data-sitekey" => $this->domainKey
		]);

		$cool->addHtml($script);
		$cool->addHtml($captch);
		return $cool;
	}

	public function validate()
	{
		if ($this->session->tryCount < $this->tryCount &&  $this->tryCount !== 0) {
			return TRUE;
		}

		if ($this->getForm()->hasOnlyValidation() || $this->enable === FALSE) {
			return TRUE;
		}
		$error = "Confirm you're not a robot ";
		if (!isset($this->getForm()->getHttpData()['g-recaptcha-response'])) {
			$this->getForm()->addError($error);
			$this->addError($error);
			return FALSE;
		}
		$validate = $this->verifyCaptcha($this->getForm()->getHttpData()['g-recaptcha-response']);

		if (!$validate) {
			$this->getForm()->addError($error);
			$this->addError($error);
			return FALSE;
		}
		$this->session->tryCount = 0;
		return TRUE;
	}

	/**
	 * @param string $captchaCode
	 * @return boolean
	 */
	private function verifyCaptcha($captchaCode): bool
	{
		if (empty($captchaCode)) {
			return FALSE;
		}

		$url = new Url("https://www.google.com/recaptcha/api/siteverify");
		$url->setQueryParameter("secret", $this->secretKey);
		$url->setQueryParameter("response", $captchaCode);

		$verify = @file_get_contents($url);
		$captcha_success = @json_decode($verify);
		return (bool)$captcha_success->success;
	}

}
