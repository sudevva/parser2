<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 24.05.2015
 * Time: 20:16
 */
namespace sys\Config\Objects;


class SystemObject extends BaseObject
{
    public function getAttributes()
    {
        return array(
            'CONTROLLER_NAMESPACE' => 'string',
            'charset' => 'string',
            'gearmanHost' => 'string',
            'ZendClientAdapter' => 'string',
            'csvSeparator' => 'string',
            'parseDir' => 'string',
        );
    }

    protected $CONTROLLER_NAMESPACE;
    /* @var string */
    protected $charset;
    /* @var string */
    protected $gearmanHost;
    protected $ZendClientAdapter;
    protected $csvSeparator;
    protected $parseDir;

    /**
     * @return mixed
     */
    public function getParseDir()
    {
        return $this->parseDir;
    }

    /**
     * @return mixed
     */
    public function getCsvSeparator()
    {
        return $this->csvSeparator;
    }

    /**
     * @return mixed
     */
    public function getZendClientAdapter()
    {
        return $this->ZendClientAdapter;
    }

    /**
     * @return string
     */
    public function getGearmanHost()
    {
        return $this->gearmanHost;
    }

    public function getControllerNamespace()
    {
        return $this->CONTROLLER_NAMESPACE;
    }

    public function getCharset()
    {
        return $this->charset;
    }
}
