<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 11.12.2015
 * Time: 23:32
 */
namespace Parsers;

use sys\Exception\ExpectedParamException;
use sys\Logger\Logger;
use sys\Registry;
use sys\Router\Loader\jsonFileLoader;
use sys\Router\Route;
use sys\Router\RouteCollection;
use sys\Router\Router;

abstract class BaseController
{
    /** @var Router */
    protected $router;
    /** @var jsonFileLoader */
    protected $fileLoader;
    /** @var Registry */
    protected $registry;

    public function __construct($dir)
    {
        $this->fileLoader = new jsonFileLoader();
        $this->fileLoader->setRouteCollection(new RouteCollection());
        $this->fileLoader->setRouteClass(new Route());
        $this->router = new Router();
        $this->router->setRouteCollection($this->fileLoader->load($dir . '/routes.json'));
        $this->registry = Registry::getInstance();
    }

    protected function processRoute(Route $route, $path)
    {
        Logger::addMessage($path);
        $route = clone $route;
        $route->method = lcfirst($route->method) . 'Action';
        $route->class = $path . '\\ContentHandler\\' . ucfirst($route->class) . 'Handler';
        if (!class_exists($route->class)) {
            throw new ExpectedParamException("No $route->class");
        }
        return $route;
    }
} 