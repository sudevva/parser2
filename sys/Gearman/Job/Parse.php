<?php

/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 13.12.2015
 * Time: 2:49
 */
class Net_Gearman_Job_Parse extends \Net_Gearman_Job_Common
{
    public function run($arg)
    {
        $registry = \sys\Registry::getInstance();
        $command = $registry->application->find('parse');

        $arguments = array(
            'command' => 'parse ',
            'urls' => $arg['arg']['url'],
            'file' => $arg['arg']['file'],
        );

        //exec('php index.php parse '.$arg['arg']['url'],$output,$returnCode);
        $greetInput = new \Symfony\Component\Console\Input\ArrayInput($arguments);
        $output = new Symfony\Component\Console\Output\ConsoleOutput();
        $returnCode = $command->run($greetInput, $output);
        foreach ($output as $otpt) {
            echo $otpt . PHP_EOL;
        };
        echo 'return code=' . ($returnCode) . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL;
        unset($command);
        return array('returnCode' => $returnCode, 'arg' => $arg );

    }
} 