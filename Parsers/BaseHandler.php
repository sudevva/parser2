<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 12.12.2015
 * Time: 16:27
 */

namespace Parsers;


use sys\Exception\ExpectedParamException;
use sys\Logger\Logger;
use sys\Registry;
use sys\Config\Config;
use Zend\Dom\Document\Query;
use Zend\Http\Response;

abstract class BaseHandler
{
    protected $url;
    protected $fileName;
    /** @var  Response */
    protected $response;
    /** @var Registry */
    protected $registry;
    /** @var Config */
    protected $config;
    /** @var \Net_Gearman_Set */
    protected $set;
    /** @var \Net_Gearman_Manager */
    protected $gearmanManager;
    /** @var   BasePageLoader */
    protected $loader;
    /** @var \Net_Gearman_Client */
    protected $client;
    /** @var  int */
    protected $jobTotal = 0;

    public function __construct($url, $file)
    {
        $this->url = $url;
        $this->fileName = $file;
        $this->registry = Registry::getInstance();
        $this->config = $this->registry->config;
        $this->set = new \Net_Gearman_Set();
        $this->gearmanManager = new \Net_Gearman_Manager($this->config->getSystemConfig()->getGearmanHost());
        $this->client = new \Net_Gearman_Client($this->registry->config->getSystemConfig()->getGearmanHost(), 1);
    }

    public function loadContent(BasePageLoader $loader = null)
    {
        if (!$loader) {
            $this->loader = new BasePageLoader();
        }
        $this->response = $this->loader->getZendResponse($this->url);
    }


    abstract function processContent();

    abstract function getFields();

    /**
     * @return int
     */
    public function getJobTotal()
    {
        return $this->jobTotal;
    }

    /**
     * @return \Net_Gearman_Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return mixed
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @return \Net_Gearman_Manager
     */
    public function getGearmanManager()
    {
        return $this->gearmanManager;
    }

    /**
     * @return BasePageLoader
     */
    public function getLoader()
    {
        return $this->loader;
    }

    /**
     * @return Registry
     */
    public function getRegistry()
    {
        return $this->registry;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return \Net_Gearman_Set
     */
    public function getSet()
    {
        return $this->set;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

} 