<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 12.12.2015
 * Time: 3:48
 */

namespace Parsers\Carid\ContentHandler;


use Parsers\BaseHandler;
use Parsers\Events\CategoryEvent;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\EventDispatcher\Event;
use sys\Logger\Logger;
use Zend\Dom\Document;
use Zend\Dom\Query;

class CategoryHandler extends BaseHandler
{
    public function getFields()
    {
    }

    public function getTaskFinishedCallback()
    {
        return function ($func, $handle, $result) {
            $event = new CategoryEvent(array('filename' => $this->fileName));
            if (!isset($result['arg']['try'])) {
                $result['arg']['try'] = 1;
            } else if ($result['arg']['try'] < 10) {
                $result['arg']['try']++;
            } else {
                return array('result' => 'fail, abort after 10 try.', 'arg' => $arg);
            }
            if (2 == $result['returnCode'] || 0 == $result['returnCode']) {
                $task = new \Net_Gearman_Task('parse', $result['arg'], null, \Net_Gearman_Task::JOB_NORMAL);
                $task->attachCallback($this->getTaskFinishedCallback());
                $this->client->addTask($task);
            } elseif ($result['returnCode'] == 1) {
                $this->registry->eventDispatcher->dispatch('parse.jobFinished', $event);
            }
            echo 'In queue: ' . $this->gearmanManager->status()[$func]['in_queue'] . ' returnCode: ' . $result['returnCode'] . PHP_EOL;
        };
    }

    public function getJobFinishedCallback()
    {
        return function ($param) {
            echo 'Finished ' . count($param) . ' tasks in(s): ' . (time() - $this->registry->startTime) . PHP_EOL;
            $event = new CategoryEvent(array('handler' => $this));
            $this->registry->eventDispatcher->dispatch('parse.finished', $event);
        };
    }

    public function processContent()
    {
        $body = $this->response->getBody();
        $domDocument = new Query($body);
        $limit = 5;
        foreach ($domDocument->execute('ul.prod_grd.three_per_row.prod_lst_square_ic li a.lst_a') as $key) {
            if (--$limit <= 0) {
                  //break;
            }
            $href = $key->getAttribute('href');
            if (!parse_url($href, PHP_URL_HOST)) {
                $urlHost = parse_url($this->url, PHP_URL_HOST);
                $urlScheme = parse_url($this->url, PHP_URL_SCHEME) . '://';
                $href = $urlScheme . $urlHost . $href;
            }
            $task = new \Net_Gearman_Task('parse',
                array(
                    'arg' => array('url' => $href, 'file' => $this->fileName)
                ), null, \Net_Gearman_Task::JOB_NORMAL);
            $task->attachCallback($this->getTaskFinishedCallback());
            $this->set->addTask($task);
        }
        if ($this->set->count() > 0) {
            $filename = $this->config->getSystemConfig()->getParseDir() . $this->fileName;
            if (file_exists($filename)) {
                rename($filename, $this->config->getSystemConfig()->getParseDir() . basename($this->fileName, '.csv') . '_toupdate.csv');
            }
            $this->set->attachCallback($this->getJobFinishedCallback());
            Logger::addMessage('Jobs pushed to server: ' . $this->set->count());
            $this->jobTotal = $this->set->count();
            $this->client->runSet($this->set);
        }
    }

    public function sendToQueue()
    {

    }
}