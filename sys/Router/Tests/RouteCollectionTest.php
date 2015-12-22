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

class RouteCollectionTest extends PHPUnit_Framework_TestCase
{
    public function testCollection()
    {
        $my = new RouteCollection();
        $route = new Route(array("path" => "/abstract", "class" => "index"));
        $my->addRoute(new Route(array("path" => "/abstract", "class" => "index")));
        $this->assertEquals(null, $my->findRouteBy("", "path"));
        $this->assertEquals($route, $my->findRouteBy("/abstract", "path"));
        $this->assertEquals(array($route), $my->getAll());
        $route = new Route(array("path" => "/abstract/class", "class" => "index"));
        $this->assertEquals($my, $my->mergeCollection((new RouteCollection())->addRoute($route)));
        $this->assertEquals($route, $my->findRouteBy("/abstract/class", "path"));
    }
} 