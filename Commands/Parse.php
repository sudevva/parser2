<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 11.12.2015
 * Time: 21:46
 */

namespace Commands;

use Commands\Listeners\ParseListener;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use sys\Exception\ExpectedParamException;
use sys\Logger\FileLogger;
use sys\Logger\Logger;
use sys\Router\Route;
use ZendGData\App\Exception;

class Parse extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('parse')
            ->setDescription('Parse url')
            ->addArgument(
                'urls',
                InputArgument::REQUIRED,
                'Url you want to parse'
            )
            ->addArgument(
                'file',
                InputArgument::OPTIONAL,
                'Puts data to specified file'
            )
            ->addOption(
                'compare',
                'c',
                InputOption::VALUE_NONE,
                'Disable compare command after finish'
            )
            ->addOption(
                'mail',
                'm',
                InputOption::VALUE_NONE,
                'Disable mailing after finish'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = $input->getArgument('urls');
        $fileName = $input->getArgument('file');
        $returnCode = 0; // Default retry job
        if ($url) {
            try {
                Logger::addMessage('Running parse: ' . $url);

                $route = $this->registry->router->match($url);
                $route->url = $url;

                $route = $this->processRoute($route);
                $controller = new $route->class(null);
                if (!$input->getOption('compare')) {
                    $this->registry->eventDispatcher->addListener('parse.finished',array(new ParseListener(),'compare'),10);
                }
                $this->registry->eventDispatcher->addListener('parse.jobFinished',array(new ParseListener(),'jobFinish'),0);
                if (!$input->getOption('mail')) {
                    $this->registry->eventDispatcher->addListener('parse.finished',array(new ParseListener(),'mail'),0);
                }
                $controller->{$route->method}($route, $fileName);

                $returnCode = 1;
            } catch (ExpectedParamException $e) {
                Logger::addMessage('Error: ' . $e->getMessage());
                $returnCode = 2; // Put into unparsed
            } catch (\Exception $e) {
                Logger::addMessage('Error: ' . $e->getMessage());
            }
        }
        FileLogger::save();
        return $returnCode;
    }

    private function processRoute(Route $route)
    {
        $route = clone $route;
        $route->method = lcfirst($route->method) . 'Action';
        $route->path = $this->config->getControllerNamespace() . ucfirst($route->class);
        $route->class = $route->path . '\\' . ucfirst($route->class) . 'Controller';
        if (!method_exists($route->class, $route->method)) {
            throw new ExpectedParamException("No $route->class or $route->class -> $route->method");
        }
        return $route;
    }
}