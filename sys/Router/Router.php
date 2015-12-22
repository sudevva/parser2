<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 12.10.2015
 * Time: 22:33
 */

namespace sys\Router;


use sys\Logger\Logger;

class Router
{
    /** @var  RouteCollection */
    private $collection;
    /** @var  abstractRoute */
    private $lastRoute;
    private $lastUrl;
    static $PREG = '([0-9a-zA-Z-\[\]/&#=\._+]+)';

    public function getRouteCollection()
    {
        return $this->collection;
    }

    public function setRouteCollection(RouteCollection $collection)
    {
        $this->collection = $collection;
    }

    public function findRouteInCollection($field, $value)
    {
        return $this->collection->findRouteBy($field, $value);
    }


    public function match($url, abstractRoute $preferRoute = null)
    {
        $this->lastUrl = $url;
        $this->lastRoute = null;
        if ($preferRoute) {
            $this->matchRoute($url, $preferRoute);
        } else {
            if (!empty($this->dbRouter)) {
                $result = $this->dbRouter->getByURL();
                if ($result) {
                    $this->lastRoute = $result;
                }
            } else {
                foreach ($this->collection->getAll() as $route) {
                    $result = $this->matchRoute($url, $route);
                    if ($result) {
                        $this->lastRoute = $route;
                    }
                }
            }
        }
        if ($this->lastRoute) {
            $result = $this->parseRoute($url, $this->lastRoute);

            if ($result) {
                return $result;
            }
        } else {
            /** @todo move part to new method */
            $result = $this->collection->findRouteBy("error", "path");
            if ($result) {
                return $result;
            }
        }
        return null;
    }

    private function parseRoute($url, abstractRoute $route)
    {
        $path = $route->path;
        $path = "^" . $path;
        $path = preg_replace('(\[\w+\])', static::$PREG, $path);
        $path = str_replace('/', '\/', $path);
        $path = str_replace('?', '\?', $path);
        $path = "/" . $path . "$/";
        $result = preg_match($path, $url, $output_array);

        if ($result) {
            $props = $route->getAll();
            $resultRoute = clone $route;
            foreach ($props as $key => $prop) {
                if (preg_match_all('(\[\w+\])', $route->path, $paramsArray)) {
                    $index = array_search($prop, $paramsArray[0], true);
                    if (false !== $index) {
                        $resultRoute->$key = $output_array[$index + 1];
                    }
                }
            }
            return $resultRoute;
        } else {
            return null;
        }
    }

    private function matchRoute($url, abstractRoute $route)
    {
        $route->url = $url;
        $path = $route->path;
        $path = "^" . $path;
        $path = preg_replace('(\[\w+\])', static::$PREG, $path);
        $path = str_replace('/', '\/', $path);
        $path = str_replace('?', '\?', $path);
        $path = "/" . $path . "$/";
        $result = preg_match($path, $url, $output_array);
        if ($result) {
            return $route;
        }
        return null;
    }
} 