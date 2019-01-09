<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 03.01.2019
 */

namespace Chomenko\ExtraForm\Extend;

class ExtendValue
{

	/**
	 * @var mixed
	 */
	private $originValue;

	/**
	 * @var mixed
	 */
	private $newValue;

	/**
	 * @var bool
	 */
	private $relationType = false;

	/**
	 * @param $originValue
	 */
	public function __construct($originValue)
	{
		$this->originValue = $originValue;
		$this->newValue = $originValue;
	}

	/**
	 * @return mixed
	 */
	public function getOriginValue()
	{
		return $this->originValue;
	}

	/**
	 * @return mixed
	 */
	public function getNewValue()
	{
		return $this->newValue;
	}

	/**
	 * @param mixed $newValue
	 * @return $this
	 */
	public function setNewValue($newValue)
	{
		$this->newValue = $newValue;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isRelationType(): bool
	{
		return $this->relationType;
	}

	/**
	 * @param bool $relationType
	 * @return $this
	 */
	public function setRelationType(bool $relationType)
	{
		$this->relationType = $relationType;
		return $this;
	}

}
