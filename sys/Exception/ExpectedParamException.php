<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 12.10.2015
 * Time: 21:47
 */

namespace sys\Exception;
use sys\Logger\Logger;

class ExpectedParamException extends \Exception
{
    public function __construct($message = "Expected param")
    {
        parent::__construct($message);
        $this->log();
    }
    private function log(){
        Logger::addMessage($this->getMessage().". IN ".$this->getFile()." AT LINE: ".$this->getLine().PHP_EOL);
    }
} 