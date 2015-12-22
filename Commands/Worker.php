<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 11.12.2015
 * Time: 21:46
 */

namespace Commands;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use sys\BackgroundProcess;
use sys\Exception\ExpectedParamException;
use sys\Logger\FileLogger;
use sys\Logger\Logger;
use sys\Registry;
use sys\Router\Route;

class Worker extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('worker:parse')
            ->setDescription('Parse url')
            ->addArgument(
                'limit',
                InputArgument::REQUIRED,
                'worker limit'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $limit = $input->getArgument('limit');
        if ($limit > 1) {
            for (; $limit > 0; $limit--) {
                BackgroundProcess::open('php index.php worker:parse 1');
            }
        } else if ($limit == 1) {
            try {
                $worker = new \Net_Gearman_Worker($this->config->getGearmanHost());
                $worker->addAbility('parse');
                $worker->beginWork();
            } catch (\Net_Gearman_Exception $e) {
                Logger::addMessage($e->getMessage());
                BackgroundProcess::open('php index.php worker:parse 1');
            }
        }
        FileLogger::save();
    }
}