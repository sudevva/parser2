<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 11.12.2015
 * Time: 21:30
 */

namespace Parsers\Carid;

use Parsers\BaseController;
use Parsers\BaseHandler;
use sys\Exception\ExpectedParamException;
use sys\Logger\Logger;
use sys\Router\Route;

class CaridController extends BaseController
{
    public function __construct()
    {
        parent::__construct(__DIR__);
    }

    public function parseAction(Route $params, $file = null)
    {
        if (!$file) {
            $file = basename($params->params,'.html').'.csv';
        }
        $route = $this->router->match($params->params);
        if ($route->class != 'error') {
            // Get correct route
            $route = $this->processRoute($route, $params->path);
            // Add current parse url
            $route->url = $params->url;

            /** @var BaseHandler $handler */
            Logger::addMessage($route->url);
            $handler = new $route->class($route->url, $file);
            // Load content of page
            $handler->loadContent();
            // Parse content of page
            $handler->processContent();

        } else {
            throw new ExpectedParamException('Cannot route url');
        }
    }
}