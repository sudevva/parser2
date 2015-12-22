<?php

/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 25.05.2015
 * Time: 22:44
 */
namespace sys;

use sys\Exception\FileException as CustomExceptionC;

/**
 * Class DependencyInjection
 * @package sys
 * @property Router\Loader\jsonFileLoader $RouteJsonFileLoader
 * @property Router\Route $Route
 * @property Router\RouteCollection $RouteCollection
 * @property Config\Loader\jsonFileLoader $configJsonFileLoader
 *
 */

final class DependencyInjection
{
    protected $dependencies = [];
    protected $postConfig = [];
    protected $components = [];
    protected $singletones = [];
    const FN_NAME_CM = '%createMethod%'; // const to access create method of class
    const PREG_DI = "/di%(.*)%di/"; // preg to get instance of  DependencyInjection container
    const PREG_CL = "/%(.*)%/"; // preg to get class name

    /**
     * @var CustomExceptionC
     */
    protected $e;

    private function postConfig($name, $instance)
    {
        if (is_object($instance)) {
            $ref = new \ReflectionClass ($this->components[$name]);
            if (isset($this->postConfig[$name])) {
                $postConfig = $this->postConfig[$name];
                foreach ($postConfig as $methodName => $confs) {
                    if (method_exists($this->components[$name], $methodName)) {
                        $method = $ref->getMethod($methodName);
                        $params = $method->getParameters();
                        $depended_objects = [];
                        if (0 < count($params)) {
                            foreach ((array)$confs as $sub_depend) {
                                $param = current($params);
                                $obj = $sub_depend;
                                if (is_string($sub_depend)) {
                                    preg_match(self::PREG_DI, $sub_depend, $di_depend);
                                    preg_match(self::PREG_CL, $sub_depend, $class_depend);
                                    if (isset($di_depend[1])) {
                                        $obj = self::__get($di_depend[1]);
                                    } elseif (isset($class_depend[1])) {
                                        if (class_exists($class_depend[1])) {
                                            $obj = new $class_depend[1];
                                        } else {
                                            $this->e = new CustomExceptionC($class_depend[1] . ' is not defined class ');
                                        }
                                    }
                                }
                                if (!is_null($obj)) {
                                    if ($param instanceof \ReflectionParameter) {
                                        $cl = $param->getClass();
                                        if (null != $cl) {
                                            $cl = $cl->getName();
                                            if ($obj instanceof $cl) {
                                                $depended_objects[] = $obj;
                                            } else {
                                                $this->e = new CustomExceptionC($name . ' cannot create with those dependencies');
                                                return null;
                                            }
                                        } elseif ($param->isDefaultValueAvailable()) {
                                            $depended_objects[] = null;
                                        } elseif (null == $cl) {
                                            $depended_objects[] = $obj;
                                        } else {
                                            $this->e = new CustomExceptionC($name . ' cannot create with those dependencies');
                                            return null;
                                        }
                                    }
                                } else {
                                    $this->e = new CustomExceptionC($name . ' cannot create with those dependencies');
                                    return null;
                                }
                                next($params);
                            }
                        }
                        if (0 < count($depended_objects)) {
                            call_user_func_array(array($instance, (string)$methodName), $depended_objects);
                        } elseif (0 == count($params)) {
                            $instance->$methodName();
                        }
                    }
                }
            }
        }
        return null;
    }

    /**
     * @param $name
     * @return null
     */
    private function create($name)
    {
        $ref = new \ReflectionClass ($this->components[$name]);
        if (isset($this->dependencies[$name])) { // Checks service has dependencies
            $dependencies = $this->dependencies[$name];
            if (isset($dependencies[self::FN_NAME_CM])) { // Checks service has createMethod || fabric
                $method = $ref->getMethod($dependencies[self::FN_NAME_CM]);
                $params = $method->getParameters();
                if (method_exists($this->components[$name],
                    $dependencies[self::FN_NAME_CM])) { // Checks is callable createMethod || fabric
                    if (isset($dependencies[$dependencies[self::FN_NAME_CM]])) { // Checks createMethod || fabric has dependencies
                        if (0 < count($params)) { // Checks createMethod || fabric has params
                            $depended_objects = [];
                            foreach ($dependencies[$dependencies[self::FN_NAME_CM]] as $sub_depend) {
                                /** @var  \ReflectionParameter $param */
                                $param = current($params);
                                $obj = $sub_depend;
                                if (is_string($sub_depend)) {
                                    preg_match(self::PREG_DI, $sub_depend, $di_depend);
                                    preg_match(self::PREG_CL, $sub_depend, $class_depend);
                                    if (isset($di_depend[1])) {
                                        $obj = self::__get($di_depend[1]);
                                    } elseif (isset($class_depend[1])) {
                                        if (class_exists($class_depend[1])) {
                                            $obj = new $class_depend[1];
                                        } else {
                                            $this->e = new CustomExceptionC($class_depend[1] . ' is not defined class ');
                                        }
                                    }
                                }
                                if (!is_null($obj)) {
                                    $cl = $param->getClass();
                                    if (null != $cl) {
                                        $cl = $cl->getName();
                                        if ($obj instanceof $cl) {
                                            $depended_objects[] = $obj;
                                        } else {
                                            $this->e = new CustomExceptionC($name . ' cannot create with those dependencies');
                                            return null;
                                        }
                                    } elseif (null == $cl) {
                                        $depended_objects[] = $obj;
                                    } else {
                                        $this->e = new CustomExceptionC($name . ' cannot create with those dependencies');
                                        return null;
                                    }
                                } elseif ($param->isDefaultValueAvailable()) {
                                    $depended_objects[] = null;
                                } else {
                                    $this->e = new CustomExceptionC($name . ' cannot create with those dependencies');
                                    return null;
                                }
                                next($params);
                            }
                            if (0 < count($depended_objects)) {
                                if ($dependencies[self::FN_NAME_CM] == '__construct') {
                                    $reflection_class = new \ReflectionClass($this->components[$name]);
                                    return $reflection_class->newInstanceArgs($depended_objects);
                                } else {
                                    return call_user_func_array($this->components[$name] . '::' . $dependencies[self::FN_NAME_CM],
                                        $depended_objects);
                                }
                            }
                        } else { // exceptional situation(Registered dependency isn't in class methods) Create service with createMethod || fabric with no params
                            $this->e = new CustomExceptionC("(Registered dependency isn't in ({$name}) methods) Trying to create service with createMethod || fabric with no params");

                            if ($dependencies[self::FN_NAME_CM] == '__construct') {
                                return new $this->components[$name];
                            } else {
                                return call_user_func($this->components[$name] . '::' . $dependencies[self::FN_NAME_CM]);
                            }
                        }
                    } else { // Create service with createMethod || fabric with no params
                        foreach ($params as $param) {
                            if (!$param->isDefaultValueAvailable()) {
                                $this->e = new CustomExceptionC("Cannot create({$name}) with no dependencies");
                                return null;
                            }
                        }
                        if ($dependencies[self::FN_NAME_CM] == '__construct') {
                            return new $this->components[$name];
                        } else {
                            return call_user_func($this->components[$name] . '::' . $dependencies[self::FN_NAME_CM]);
                        }
                    }
                } else { //Service has no createMethod || fabric function
                    $this->e = new CustomExceptionC($name . ' has no ' . $dependencies[self::FN_NAME_CM] . ' method');
                    return null;
                }
            }
        } else { // If service has no dependencies
            if ($ref->isInstantiable()) { // trying to create new service Instance via __constructor
                return new $this->components[$name];
            } else {
                $this->e = new CustomExceptionC("Cannot create($name}) with no __construct and dependencies");
                return null;
            }
        }
        return null;
    }

    public function __get($name)
    {
       return $this->get($name);
    }
    public function get($name){
        if (isset($this->singletones[$name]) && !is_object($this->singletones[$name])) { // Checks service is not prepared
            $obj = $this->create($name);
            if (null != $obj) {
                $this->postConfig($name, $obj);
                unset($this->components[$name]);
                unset($this->dependencies[$name]);
                $this->singletones[$name] = $obj;
                return $this->singletones[$name];
            }
        } elseif (isset($this->singletones[$name]) && is_object($this->singletones[$name])) { // Checks service is prepared
            return $this->singletones[$name];
        } elseif (isset($this->components[$name])) {  // Checks Instantiable is prepared
            $obj = $this->create($name);
            if (null != $obj) {
                $this->postConfig($name, $obj);
                return $obj;
            }
        }
        return null;
    }

    public function setPostConfig($nameToGet, $function, $dependency)
    {
        if (isset($this->components[$nameToGet])) {
            $this->postConfig[$nameToGet][$function] = $dependency;
        } else {
            $this->e = new CustomExceptionC('Call registerClass({$nameToGet}) first');
            return null;
        }
        return $this;
    }

    public function setDependency($nameToGet, $function, $dependency)
    {
        if (isset($this->components[$nameToGet])) {
            $this->dependencies[$nameToGet][$function] = $dependency;
        } else {
            $this->e = new CustomExceptionC('Call registerClass({$className}) first');
        }
        return $this;
    }

    public function registerClass($nameToGet, $className, $singleton = false)
    {
        if (class_exists($className)) {
            if ($singleton) {
                $this->singletones[$nameToGet] = true;
            }
            $this->components[$nameToGet] = $className;
        } else {
            $this->e = new CustomExceptionC("Cannot find ({$className}) class");
        }
        return $this;
    }

    public function __clone()
    {
    }
}