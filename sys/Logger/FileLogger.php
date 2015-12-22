<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 12.10.2015
 * Time: 22:05
 */

namespace sys\Logger;


class FileLogger extends Logger
{
    public static function save()
    {
        $result = '';
        foreach (static::$_log as $log) {
           //echo $log. PHP_EOL;
           $result .= $log . PHP_EOL;
        }
        file_put_contents('logs/log.txt', $result . PHP_EOL, FILE_APPEND);
        static::$_log = array();
    }
} 