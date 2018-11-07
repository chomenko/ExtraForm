<?php
/**
 * Author: Mykola Chomenko
 * Email: mykola.chomenko@dipcom.cz
 * Created: 03.06.2018 22:28
 */

namespace Chomenko\ExtraForm\Exception;


class BuildException extends \Exception
{

    public static function AddItemFailed(){
        throw new BuildException('Form element must be string or array');
    }

}