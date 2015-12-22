<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 18.12.2015
 * Time: 22:09
 */
namespace Commands\Listeners;

use Parsers\BaseHandler;
use Parsers\Events\CategoryEvent;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\EventDispatcher\Event;
use sys\Mailer;

class ParseListener extends BaseListener
{
    public function compare(CategoryEvent $event)
    {
        /** @var BaseHandler $handler */
        $handler = $event->getParam('handler');
        if ($handler) {
            $command = $this->registry->application->find('compare');
            $arguments = array(
                'command' => 'compare',
                'file' => $handler->getFileName(),
            );
            $input = new ArrayInput($arguments);
            $output = new ConsoleOutput();
            $command->run($input, $output);
        }
    }

    public function mail(CategoryEvent $event)
    {
        /** @var BaseHandler $handler */
        $handler = $event->getParam('handler');
        if ($handler) {
            $dateFormat = 'Y-m-d h:i:s';
            $mailer = new Mailer();
            $mailer->setGroup('product');
            $message = $mailer->getMessage();
            $message->setSubject($handler->getUrl() . ' ' . date($dateFormat));
            $body = 'Script started at: ' . date($dateFormat, $this->registry->startTime) . PHP_EOL;
            $body .= 'Script finished at: ' . date($dateFormat) . '; Total execute time: ' . date('i:s', time() - $this->registry->startTime) . PHP_EOL;
            $body .= 'Parsed urls: ' . $handler->getJobTotal() . PHP_EOL;
            $message->setBody($body);
            $fileParts = pathinfo($this->config->getParseDir() . $handler->getFileName());

            $fname = $this->config->getParseDir() . $handler->getFileName();
            file_exists($fname) ? $message->attach(\Swift_Attachment::fromPath($fname)) : null;

            $fname = $this->config->getParseDir() . $fileParts['filename'] . '_disappeared_products.' . $fileParts['extension'];
            file_exists($fname) ? $message->attach(\Swift_Attachment::fromPath($fname)) : null;

            $fname = $this->config->getParseDir() . $fileParts['filename'] . '_newProducts.' . $fileParts['extension'];
            file_exists($fname) ? $message->attach(\Swift_Attachment::fromPath($fname)) : null;

            $fname = $this->config->getParseDir() . $fileParts['filename'] . '_recently_reviewed_products.' . $fileParts['extension'];
            file_exists($fname) ? $message->attach(\Swift_Attachment::fromPath($fname)) : null;
            $mailer->send();
        }
    }

    public function jobFinish(CategoryEvent $event)
    {
    }
} 