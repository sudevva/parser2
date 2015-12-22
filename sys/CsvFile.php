<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 17.12.2015
 * Time: 3:01
 */

namespace sys;


use sys\Exception\ExpectedParamException;
use sys\Logger\Logger;

class CsvFile
{
    static function putInFile($content, $file, $mode = 'a')
    {
        $config = Registry::getInstance()->config->getSystemConfig();
        $fileName = $config->getParseDir() . $file;
        $separator = $config->getCsvSeparator();
        if (is_dir(dirname($fileName))) {
            $file = fopen($fileName, $mode);
            if (count($content)>0) {
                if (filesize($fileName) == 0) {
                    fputcsv($file, array_keys(reset($content)), $separator);
                }
                foreach ($content as $value) {
                    fputcsv($file, $value, $separator);
                }
            } else {
                if (filesize($fileName) == 0) {
                    fputcsv($file, array_keys($content), $separator);
                }
                fputcsv($file, $content, $separator);
            }
            fclose($file);
        } else {
            throw new ExpectedParamException('Cant open directory or file ' . $fileName);
        }
    }

    static function readFile($file)
    {
        $config = Registry::getInstance()->config->getSystemConfig();
        $dataNew = array();
        $heading = array();
        foreach (file($file) as $key) {
            if (empty($heading)) {
                $heading = str_getcsv($key, $config->getCsvSeparator());
            } else {
                $product = str_getcsv($key, $config->getCsvSeparator());
                foreach ($heading as $key => $value) {
                    $dataNew[$product[0]][$value] = str_getcsv($product[$key]);
                }
            }
        }
        return $dataNew;
    }
} 