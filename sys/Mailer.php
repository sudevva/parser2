<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 20.12.2015
 * Time: 21:18
 */

namespace sys;


class Mailer extends \Swift_Mailer
{
    /** @var Config\Objects\MailerGroupObject */
    protected $groups;
    /** @var Config\Objects\MailerObject */
    protected $mailerConfig;
    /** @var \Swift_Message */
    protected $message;

    public function __construct()
    {
        $this->registry = Registry::getInstance();
        $this->mailerConfig = $this->registry->config->getMailer();
        $this->message = \Swift_Message::newInstance();
        echo $this->mailerConfig->getSender().PHP_EOL;
        $this->message->setFrom($this->mailerConfig->getSender());
        $this->transport = \Swift_SmtpTransport::newInstance($this->mailerConfig->getHost(), $this->mailerConfig->getPort(),'ssl')
            ->setUsername($this->mailerConfig->getUser())
            ->setPassword($this->mailerConfig->getPass());
        parent::__construct($this->transport);
    }

    public function send()
    {
        parent::send($this->message);
    }

    public function setGroup($name)
    {
        $mailTo = $this->mailerConfig->getMailToByGroupName($name);
        if ($mailTo) {
            $this->message->setTo($mailTo->getMailTo());
            return true;
        }
        return false;
    }

    /**
     * @return \Swift_Message
     */
    public function getMessage()
    {
        return $this->message;
    }

} 