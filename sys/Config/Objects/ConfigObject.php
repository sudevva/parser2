<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 24.05.2015
 * Time: 20:16
 */
namespace sys\Config\Objects;


class ConfigObject extends BaseObject
{
    /** @var  SystemObject */
    protected $system;
    /** @var  ProxyObject[] */
    protected $proxies;
    /** @var   MailerObject */
    protected $mailer;

    public function getAttributes()
    {
        return array(
            'proxies' => 'array<sys\\Config\\Objects\\ProxyObject>',
            'system' => 'sys\\Config\\Objects\\SystemObject',
            'mailer' => 'sys\\Config\\Objects\\MailerObject',
        );
    }

    /**
     * @return MailerObject
     */
    public function getMailer()
    {
        return $this->mailer;
    }

    /**
     * @return SystemObject
     */
    public function getSystemConfig()
    {
        return $this->system;
    }

    /**
     * @return ProxyObject[]
     */
    public function getProxies()
    {
        return $this->proxies;
    }

    /** @return  ProxyObject */
    public function getRandProxy()
    {
        return $this->proxies[rand(0, count($this->proxies) - 1)];
    }
}
