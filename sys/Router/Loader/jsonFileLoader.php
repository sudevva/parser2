<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 12.10.2015
 * Time: 23:03
 */
namespace sys\Router\Loader;

use sys\Exception\FileException as FileException;

/**
 * use to get RouteCollection from *.json file
 */
class jsonFileLoader extends abstractLoader
{


    /**
     * @param string $file
     * @return null|\sys\Router\RouteCollection
     */
    public function load($file)
    {
        if (file_exists($file) && isset($this->class) && isset($this->collection)) {
            $content = file_get_contents($file);
            $content = json_decode($content, true);
            if (isset($content["routes"])) {
                foreach ($content["routes"] as $value) {
                    $this->collection->addRoute(new $this->class($value));
                }
            } else {
                new FileException("Your route file isn't correct");
                return null;
            }
        } else {
            new FileException("Your route file isn't exists");
            return null;
        }
        return $this->collection;
    }
} 