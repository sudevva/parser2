<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 15.10.2015
 * Time: 21:01
 */
namespace sys\Router;

use sys\Exception\ExpectedParamException;
use \sys\Exception\UnexpectedParamException;

abstract class abstractRoute
{
    public $path = "/";
    public $class = "index";
    public $method = "index";
    public $params = "";
    public $url = "";

    public function __construct(array $params = null)
    {
        if ($params) {
            $this->setAll($params);
        }

    }

    public function setAll(array $params)
    {
        if (isset($params["path"]) && isset($params["class"])) {
            foreach ($params as $name => $value) {
                if (property_exists($this, $name)) {
                    $this->$name = $value;
                } else {
                    throw new UnexpectedParamException("a");
                }
            }
        }else{
            throw new ExpectedParamException("a");
        }

    }

    public function getAll()
    {
        return array("path" => $this->path, "class" => $this->class, "method" => $this->method, "params" => $this->params);
    }
}