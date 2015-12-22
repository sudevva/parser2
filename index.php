<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 11.12.2015
 * Time: 23:24
 */
namespace main;
require __DIR__ . '/vendor/autoload.php';

use Commands\Compare;
use Commands\Worker;
use Symfony\Component\Console\Application;
use Commands\Parse;
use Symfony\Component\EventDispatcher\EventDispatcher;
use sys\Config\Config;
use sys\Logger\FileLogger;
use sys\Logger\Logger;
use sys\Registry;
use sys\Router\Route;
use sys\Router\RouteCollection;
use sys\Router\Router;
use sys\Router\Loader\jsonFileLoader as RouterLoader;
use sys\Config\Loader\jsonFileLoader as ConfigLoader;

$registry = Registry::getInstance();
$configLoader = new ConfigLoader();
$configLoader ->setConfigObject(new Config());

$registry->config =$configLoader->load('config/config.json');
$registry->router = new Router();
$registry->eventDispatcher = new EventDispatcher();
$routeLoader = new RouterLoader();
$routeLoader->setRouteCollection(new RouteCollection());
$routeLoader->setRouteClass(new Route());
$registry->router->setRouteCollection($routeLoader->load("config/routes.json"));
try {
    $application = new Application();
    $registry->application = $application;
    $application->add(new Parse());
    $application->add(new Worker());
    $application->add(new Compare());
    $application->run();
} catch (\Exception $e) {
    echo "<pre>";
    echo Logger::toString();
    FileLogger::save();
    echo "</pre>";
}