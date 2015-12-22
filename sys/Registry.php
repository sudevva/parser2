<?php

/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 24.05.2015
 * Time: 17:33
 */
namespace sys;

use Symfony\Component\Console\Application;
use Symfony\Component\EventDispatcher\EventDispatcher;
use sys\Router\Router;
use sys\Config\Config;

/**
 * @property DependencyInjection $di
 * @property Config $config
 * @property Application $application
 * @property Router $router
 * @property int $startTime
 * @property EventDispatcher $eventDispatcher
 */
final class Registry
{
    private $data = array();
    private static $_instance = null;

    private function __construct()
    {
        $this->startTime = time();
    }

    protected function __clone()
    {
    }

    /**
     * @return Registry
     */
    static public function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __get($key)
    {
        return $this->get($key);
    }

    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    public function get($key)
    {
        return (isset($this->data[$key]) ? $this->data[$key] : null);
    }

    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function has($key)
    {
        return isset($this->data[$key]);
    }
}
