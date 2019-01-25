<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\ExtraForm\Controls\Traits;

trait SizeInputs
{

	/**
	 * @var string|null
	 */
	protected $inputSize;

	/**
	 * @return $this
	 */
	public function setSizeLG()
	{
		$this->inputSize = "form-control-lg";
		return $this;
	}

	/**
	 * @return $this
	 */
	public function setSizeDefault()
	{
		$this->inputSize = NULL;
		return $this;
	}

	/**
	 * @return $this
	 */
	public function setSizeSM()
	{
		$this->inputSize = "form-control-sm";
		return $this;
	}

}
