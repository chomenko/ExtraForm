<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 05.01.2019
 */

namespace Chomenko\ExtraForm\Extend\Date;

use Chomenko\ExtraForm\Extend\EntityExtend;
use Chomenko\ExtraForm\Extend\ExtendValue;

class DateFormat extends EntityExtend
{

	const OPTION_KEY = "@dateFormat";

	/**
	 * @var string
	 */
	private $format;

	/**
	 * @param string $format
	 */
	public function __construct(string $format)
	{
		$this->format = $format;
	}

	/**
	 * @return string
	 */
	public function getFormat(): string
	{
		return $this->format;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return self::OPTION_KEY;
	}

	/**
	 * @param object $entity
	 * @param ExtendValue $value
	 * @return mixed|void
	 */
	public function executeData(object $entity, ExtendValue $value)
	{
		parent::executeData($entity, $value);
		$value->setNewValue(\DateTime::createFromFormat($this->format, $value->getOriginValue()));
	}

}
