<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\ExtraForm\Extend\Pair;

interface IPairs
{

	const OPTION_KEY = "@pair";

	/**
	 * @param string $name
	 */
	public function setPattern(string $name);

	/**
	 * @param array $items
	 * @return mixed
	 */
	public function setItems(array $items);

	/**
	 * @return mixed
	 */
	public function getItems();

	/**
	 * @param mixed $value
	 * @return mixed|object
	 */
	public function getItem($value);

}
