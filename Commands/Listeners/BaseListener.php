<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 18.12.2015
 * Time: 22:13
 */

namespace Commands\Listeners;


use sys\Registry;

class BaseListener
{
    /** @var Registry */
    protected $registry;
    /** @var \sys\Config\Objects\SystemObject */
    protected $config;

    public function __construct()
    {
        $this->registry = Registry::getInstance();
        $this->config = Registry::getInstance()->config->getSystemConfig();
    }
} 