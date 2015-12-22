<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 12.10.2015
 * Time: 23:03
 */
namespace sys\Config\Loader;

use sys\Exception\ExpectedParamException;
use sys\Exception\FileException as FileException;
use sys\Config\Config;

/**
 * use to get Config from *.json file
 */
class jsonFileLoader extends abstractLoader
{
    /**
     * @param string $file
     * @return null|Config
     */
    public function load($file)
    {
        if (file_exists($file) && isset($this->config)) {
            $content = file_get_contents($file);
            $content = json_decode($content, true);
            if ($content) {
                $this->config->load($content);
            } else {
                new FileException("Your route file isn't correct");
                return null;
            }
        } else if(!file_exists($file)){
            new FileException("Your route file isn't exists");
            return null;
        } else if(!isset($this->config)){
            new ExpectedParamException("Dont defined class");
            return null;
        }
        return $this->config;
    }
} 