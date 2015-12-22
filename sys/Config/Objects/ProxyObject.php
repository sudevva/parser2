<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 24.05.2015
 * Time: 20:16
 */
namespace sys\Config\Objects;


class ProxyObject extends BaseObject
{
    public function getAttributes()
    {
        return array(
            'host' => 'string',
            'port' => 'string',
            'user' => 'string',
            'pass' => 'string',
        );
    }

    protected $host;
    protected $port;
    protected $user;
    protected $pass;

    /**
     * @return mixed
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return mixed
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return mixed
     */
    public function getPass()
    {
        return $this->pass;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

}
