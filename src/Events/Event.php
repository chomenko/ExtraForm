<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 27.12.2018
 */

namespace Chomenko\ExtraForm\Events;

class Event
{

	/**
	 * @var string
	 */
	private $type;

	/**
	 * @var callable
	 */
	private $callable;


	public function __construct(string $type, callable $callable)
	{
		$this->type = $type;
		$this->callable = $callable;
	}

	/**
	 * @param array $args
	 * @return mixed
	 */
	public function emit(array $args = [])
	{
		return call_user_func_array($this->callable, $args);
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return $this->type;
	}

	/**
	 * @param string $type
	 * @return bool
	 */
	public function hasType(string $type): bool
	{
		return $type === $this->type;
	}

	/**
	 * @return callable
	 */
	public function getCallable(): callable
	{
		return $this->callable;
	}

}
