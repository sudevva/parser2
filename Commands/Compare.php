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
use sys\CsvFile;
use sys\Exception\ExpectedParamException;
use sys\Logger\FileLogger;
use sys\Logger\Logger;
use sys\Registry;
use sys\Router\Route;

class Compare extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('compare')
            ->setDescription('compare files')
            ->addArgument(
                'file',
                InputArgument::REQUIRED,
                'compare files'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');
        $dir = $this->config->getParseDir();
        var_dump($dir, $file);
        $fileNew = $dir . $file;
        $baseFileName = basename($file, '.csv');
        $fileOld = $dir . $baseFileName . '_toupdate.csv';
        if (file_exists($fileNew) && file_exists($fileOld)) {
            $dataNew = CsvFile::readFile($fileNew);
            $dataOld = CsvFile::readFile($fileOld);
            $disappearedProducts = array_diff_assoc($dataOld, $dataNew);
            $newProducts = array_diff_assoc($dataNew, $dataOld);
            $reviewsCountChanged = array();
            foreach ($dataNew as $key => $newProd) {
                if (isset($dataOld[$key])) {
                    if (count($newProd['reviews']) != count($dataOld[$key]['reviews'])) {
                        $reviewsCountChanged[]=array('sku' => $newProd['sku'][0],
                            'count' => count($newProd['reviews']) - count($dataOld[$key]['reviews']));
                    }
                } else {

                }
            }
            $disappearedProducts = array_keys($disappearedProducts);
            $newProducts = array_keys($newProducts);
            array_walk($disappearedProducts,function(&$value){$value= array('sku'=>$value);});
            array_walk($newProducts,function(&$value){$value= array('sku'=>$value);});
            var_dump($disappearedProducts);
            var_dump($newProducts);
            CsvFile::putInFile($disappearedProducts, $baseFileName . '_disappeared_products.csv','w+');
            CsvFile::putInFile($newProducts, $baseFileName . '_newProducts.csv','w+');
            CsvFile::putInFile($reviewsCountChanged, $baseFileName . '_recently_reviewed_products.csv','w+');
        }
        FileLogger::save();
    }
}