<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 14.10.2015
 * Time: 22:08
 */

namespace sys\router\Tests;

use PHPUnit_Framework_TestCase;
use sys\Exception\ExpectedParamException;
use sys\Exception\UnexpectedParamException;
use sys\Router\Route;

class RouteTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provider
     */
    public function testRoute($path, $result)
    {
        $my = new Route($path);
        $this->assertEquals($result, $my->getAll());
    }

    /**
     * @dataProvider provider
     */
    public function testSetAll($path, $result)
    {
        $my = new Route();
        $this->assertNotEmpty($my->getAll());
        $my->setAll($path);
        $this->assertEquals($result, $my->getAll());
    }

    public function testException()
    {
        $my = new Route();
        try {
            $my->setAll(array("path" => "a", "aclass" => "b", "asd" => "aaa"));
        } catch (ExpectedParamException $e) {
        }
        try {
            $my->setAll(array("path" => "a", "class" => "b", "asd" => "aaa"));
        } catch (UnexpectedParamException $e) {
        }
    }

    public function provider()
    {
        return array(
            array(array("path" => "2", "class" => "3", "method" => "4", "params" => "5"), array("path" => "2", "class" => "3", "method" => "4", "params" => "5")),
            array(array("path" => "a", "class" => "b", "method" => "c", "params" => "d"), array("path" => "a", "class" => "b", "method" => "c", "params" => "d"))
        );
    }
} 