<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 12.12.2015
 * Time: 0:03
 */

namespace Commands;


use Symfony\Component\Console\Command\Command;
use sys\Registry;

class BaseCommand extends Command
{
    /** @var Registry */
    protected $registry;
    /** @var \sys\Config\Objects\SystemObject  */
    protected $config;

    public function __construct()
    {
        $this->registry = Registry::getInstance();
        $this->config = Registry::getInstance()->config->getSystemConfig();
        parent::__construct();
    }
} 