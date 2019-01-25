<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 */

namespace Chomenko\ExtraForm\Builds;


use Nette\Utils\Html;

class HtmlUtility
{

	/**
	 * @param Html $el
	 * @param $class
	 */
	public static function addClass(Html $el, $class)
	{
		$_class = $el->getAttribute('class');
		$_class = explode( " ", $_class);
		if(is_array($class)){
			$_class = array_merge($_class, $class);
		}elseif(is_string($class)){
			$_class[] = $class;
		}
		$el->setAttribute('class', trim(implode(" ", $_class), " "));
	}

	/**
	 * @param Html $to
	 * @param Html $html
	 */
	public static function prependHtml(Html $to, Html $html)
	{
		$children = $to->getChildren();
		if(empty($children)){
			$to->setHtml($html);
		}else{
			$to->setHtml($html);
			foreach ($children as $child){
				$to->addHtml($child);
			}
		}
	}



}