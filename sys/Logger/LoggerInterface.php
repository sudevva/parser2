<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 12.10.2015
 * Time: 21:53
 */

namespace sys\Logger;


interface LoggerInterface
{
    /**
     * @param $message string
     * @return mixed
     */
    static function addMessage($message);

    /**
     * @return string
     */
    static function toString();
} 