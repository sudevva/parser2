<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 15.10.2015
 * Time: 20:56
 */

namespace sys\Config\Loader;

use sys\Config\Config;

abstract class abstractLoader
{
    /** @var  Config $config */
    protected $config;

    public function setConfigObject(Config $class)
    {
        $this->config = $class;
    }

    /**
     * @param $params string
     * @return Config
     */
    abstract function load($params);
} 