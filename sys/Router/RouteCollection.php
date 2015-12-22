<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 12.10.2015
 * Time: 22:34
 */

namespace sys\Router;


class RouteCollection
{
    /** @var AbstractRoute[] */
    private $routes = array();

    public function mergeCollection(self $collection)
    {
        $this->routes = array_merge($collection->getAll(), $this->routes);
        return $this;
    }

    public function addRoute(abstractRoute $route)
    {
        $this->routes[] = $route;
        return $this;
    }

    /**
     * Find route by path value
     * @param $valueToFind string
     * @param $fieldName string
     * @return Route|null
     */
    public function findRouteBy($valueToFind, $fieldName)
    {
        foreach ($this->routes as $route) {
            if (property_exists($route, $fieldName)) {
                if ($route->$fieldName == $valueToFind) {
                    return $route;
                    break;
                }
            }
        }
        return null;
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->routes;
    }
} 