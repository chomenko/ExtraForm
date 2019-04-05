<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\ExtraForm\Exception;

use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\ConstraintValidatorInterface;

class Exception extends \Exception
{

	/**
	 * @return Exception
	 */
	public static function addItemFailed()
	{
		return new Exception('Form element must be string or array');
	}

	/**
	 * @param mixed $parameter
	 * @return Exception
	 */
	public static function typeStringOrObject($parameter)
	{
		return new Exception("EntityForm first parameter must be string or object. Your type is '" . gettype($parameter) . "'");
	}

	/**
	 * @param string $class
	 * @return Exception
	 */
	public static function notAttached(string $class)
	{
		return new Exception("'{$class}' was not attached to the form");
	}

	/**
	 * @param string $class
	 * @return Exception
	 */
	public static function noIdentifier(string $class)
	{
		return new Exception("No identifier specified in '{$class}'");
	}

	/**
	 * @param string $class
	 * @return Exception
	 */
	public static function emptyIdentifier(string $class)
	{
		return new Exception("Empty identifier in '{$class}'");
	}

	/**
	 * @param string $field
	 * @param string $inClass
	 * @return Exception
	 */
	public static function doesNotExistField(string $field, string $inClass)
	{
		return new Exception("Field '{$field}' doesn't exist in '{$inClass}'");
	}

	/**
	 * @param string $field
	 * @param string $inClass
	 * @return Exception
	 */
	public static function fieldDoesNotString(string $field, string $inClass)
	{
		return new Exception("Unsupported field type '{$field}' in '{$inClass}'");
	}

	/**
	 * @param string $field
	 * @return Exception
	 */
	public static function fieldMustCollection(string $field)
	{
		$collection = Collection::class;
		return new Exception("Field '{$field}' must be instance of '{$collection}'");
	}

	/**
	 * @param object $service
	 * @return Exception
	 */
	public static function constraintServiceValidatorMustInstance(object $service)
	{
		$service = get_class($service);
		$interface = ConstraintValidatorInterface::class;
		return new Exception("Constraint service validator '{$service}' must by implement '{$interface}' ");
	}

	/**
	 * @param string $name
	 * @return Exception
	 */
	public static function constraintServiceUnregistered(string $name)
	{
		return new Exception("Constraint service validator '{$name}' unregistered. Check configuration.");
	}

}
