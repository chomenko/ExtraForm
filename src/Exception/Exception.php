<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 03.06.2018 22:28
 */

namespace Chomenko\ExtraForm\Exception;

use Doctrine\Common\Collections\Collection;

class Exception extends \Exception
{

	/**
	 * @return Exception
	 */
	public static function AddItemFailed(){
		return new Exception('Form element must be string or array');
	}

	/**
	 * @param $parameter
	 * @return Exception
	 */
    public static function typeStringOrObject($parameter){
    	return new Exception("EntityForm first parameter must be string or object. Your type is '".gettype($parameter)."'");
    }

	/**
	 * @param string $class
	 * @return Exception
	 */
	public static function notAttached(string $class){
		return new Exception("'{$class}' was not attached to the form");
	}

	/**
	 * @param string $class
	 * @return Exception
	 */
	public static function noIdentifier(string $class){
		return new Exception("No identifier specified in '{$class}'");
	}

	/**
	 * @param string $class
	 * @return Exception
	 */
	public static function emptyIdentifier(string $class){
		return new Exception("Empty identifier in '{$class}'");
	}

	/**
	 * @param string $field
	 * @param string $inClass
	 * @return Exception
	 */
	public static function doesNotExistField(string $field, string $inClass){
		return new Exception("Field '{$field}' doesn't exist in '{$inClass}'");
	}

	/**
	 * @param string $field
	 * @param string $inClass
	 * @return Exception
	 */
	public static function fieldDoesNotString(string $field, string $inClass){
		return new Exception("Unsupported field type '{$field}' in '{$inClass}'");
	}

	/**
	 * @param string $field
	 * @return Exception
	 */
	public static function fieldMustCollection(string $field){
		$collection = Collection::class;
		return new Exception("Field '{$field}' must be instance of '{$collection}'");
	}

}
