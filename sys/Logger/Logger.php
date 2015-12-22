<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 12.10.2015
 * Time: 21:56
 */

namespace sys\Logger;


abstract class Logger implements LoggerInterface
{
    protected static $_log = array();
    protected static $log = "";

    static function addMessage($message)
    {
        static::$_log[] = $message;
        echo $message.PHP_EOL;
        return $message;
    }

    static function toString($del = "<br/>")
    {
        foreach (static::$_log as $log) {
            static::$log .= $log . $del;
        }
        return static::$log;
    }
} 