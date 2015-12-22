<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 24.05.2015
 * Time: 20:16
 */
namespace sys\Config\Objects;


class MailerGroupObject extends BaseObject
{
    public function getAttributes()
    {
        return array(
            'mailTo' => 'string',
        );
    }

    /** @var  array */
    protected $mailTo;

    /**
     * @return array
     */
    public function getMailTo()
    {
        return $this->mailTo;
    }

}
