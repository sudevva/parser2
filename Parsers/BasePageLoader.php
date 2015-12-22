<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 12.12.2015
 * Time: 12:22
 */

namespace Parsers;


use sys\Logger\Logger;
use sys\Registry;
use Zend\Http\Client;

class BasePageLoader extends Client
{
    protected $registry;
    protected $response;

    public function __construct()
    {
        parent::__construct();
        $this->registry = Registry::getInstance();
    }
    public function getZendResponse($url){
        $this->setUri($url);
        $proxy = $this->registry->config->getRandProxy();
        Logger::addMessage('Proxy: '.$proxy->getHost());
        Logger::addMessage('URL: '.$url);
        $this->setOptions(array(
            'proxy_host' => $proxy->getHost(),
            'proxy_port' => $proxy->getPort(),
            'useragent' => 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.106 Safari/537.36',
            'timeout' => 10,
            'adapter'      => $this->registry->config->getSystemConfig()->getZendClientAdapter(),
            'ssltransport' => 'tls'
        ));
       return $this->send();
    }
}