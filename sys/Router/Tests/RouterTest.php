<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 14.10.2015
 * Time: 22:08
 */

namespace sys\router\Tests;

use PHPUnit_Framework_TestCase;
use sys\Router\Route;
use sys\Router\RouteCollection;
use sys\Router\Router;

class RouterTest extends PHPUnit_Framework_TestCase
{
    public function testRouter()
    {
        $my = new RouteCollection();
        $route3 = new Route(array("path" => "error", "class" => "index"));
        $my->addRoute($route3);
        $route = new Route(array("path" => "/[abstract]", "class" => "[abstract]"));
        $my->addRoute($route);
        $this->assertEquals(null, $my->findRouteBy("", "path"));
        $this->assertEquals($route, $my->findRouteBy("/[abstract]", "path"));
        $this->assertEquals(array($route3,$route), $my->getAll());
        $route2 = new Route(array("path" => "/abstract/[class]", "class" => "[class]"));
        $this->assertEquals($my, $my->mergeCollection((new RouteCollection())->addRoute($route2)));
        $found = $my->findRouteBy("/abstract/[class]", "path");
        $this->assertEquals($route2, $my->findRouteBy("/abstract/[class]", "path"));
        $router = new Router();
        $router->setRouteCollection($my);

        $this->assertEquals(new Route(array("path" => "/[abstract]", "class" => "abstract")), $router->match("/abstract"));
        $this->assertEquals($route3, $router->match("/abstract/class/?a"));
        $this->assertEquals(new Route(array("path" => "/abstract/[class]", "class" => "tempor")), $router->match("/abstract/tempor"));
    }
} 