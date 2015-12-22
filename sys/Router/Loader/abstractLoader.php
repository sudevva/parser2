<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 15.10.2015
 * Time: 20:56
 */

namespace sys\router\Loader;


use sys\Router\Route;
use sys\Router\RouteCollection;

abstract class abstractLoader
{
    /** @var  RouteCollection $collection */
    protected  $collection;
    /** @var  Route $class */
    protected $class;

    public function setRouteClass(Route $class)
    {
        $this->class = $class;
    }

    /**
     * @param RouteCollection $collection
     * @return $this
     */
    public function setRouteCollection(RouteCollection $collection)
    {
        $this->collection = $collection;
        return $this;
    }

    /**
     * @param $params string
     * @return RouteCollection
     */
    abstract function load($params);
} 