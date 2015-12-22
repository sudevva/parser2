<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 24.05.2015
 * Time: 20:16
 */
namespace sys\Config\Objects;


class MailerObject extends BaseObject
{
    public function getAttributes()
    {
        return array(
            "host" => "string",
            "port" => "int",
            "user" => "string",
            "pass" => "string",
            "sender" => "string",
            "groups" => "array<sys\\Config\\Objects\\MailerGroupObject>",
        );
    }

    /* @var string */
    protected $host;
    /* @var string */
    protected $port;
    /* @var string */
    protected $user;
    /* @var string */
    protected $pass;
    /* @var MailerGroupObject */
    protected $groups;
    /* @var string */
    protected $sender;


    /**
     * @param $name
     * @return MailerGroupObject|null
     */
    public function getMailToByGroupName($name)
    {
        return isset($this->groups[$name]) ? $this->groups[$name] : null;
    }

    /**
     * @return MailerGroupObject[]
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return string
     */
    public function getPass()
    {
        return $this->pass;
    }

    /**
     * @return string
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getSender()
    {
        return $this->sender;
    }

}
