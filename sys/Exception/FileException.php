<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 12.10.2015
 * Time: 21:47
 */

namespace sys\Exception;
use sys\Logger\Logger;

/** @todo log to abstract */
class FileException extends \Exception
{
    public function __construct($message = null)
    {
        parent::__construct($message);
        $this->log();
    }
    private function log(){
        Logger::addMessage($this->getMessage().". IN ".$this->getFile()." AT LINE: ".$this->getLine().PHP_EOL);
    }
} 