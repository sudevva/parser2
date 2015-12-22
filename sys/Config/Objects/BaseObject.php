<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 24.05.2015
 * Time: 19:19
 */
namespace sys\Config\Objects;

use sys\Exception\ConfigMissingException;

abstract class BaseObject
{
    public function __construct(array $arrayObject = null)
    {
        if (is_array($arrayObject)) {
            $this->parseArray($arrayObject);
        }

    }

    public function load(array $arrayObject = null)
    {
        if (is_array($arrayObject)) {
            $this->parseArray($arrayObject);
        }
        return $this;
    }

    private function get($key)
    {
        try {
            if (isset($this->$key)) {
                return $this->$key;
            } else {
                throw new ConfigMissingException("Please setup config file, missing key [database]=>${key} </br> ");
            }
        } catch (ConfigMissingException $e) {
            return null;
        }
    }

    public function parseArray($arrayObject)
    {
        $attributeList = $this->getAttributes();
        foreach ($attributeList as $attributeName => $attributeType) {
            if (array_key_exists($attributeName, $arrayObject)) {
                switch ($attributeType) {
                    case 'string':
                        $this->$attributeName = $arrayObject[$attributeName];
                        break;
                    case 'int':
                        $this->$attributeName = intval($arrayObject[$attributeName]);
                        break;
                    case 'bool':
                        $this->$attributeName = $arrayObject[$attributeName] === true || $arrayObject[$attributeName] === false ? $arrayObject[$attributeName] : null;
                        break;
                    case 'path':
                        $this->$attributeName = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR,
                            $arrayObject[$attributeName]);
                        break;
                    default:
                        $matches = null;
                        if (preg_match('/^array<([^>]+)>$/', $attributeType, $matches)) {
                            $attributeType = $matches[1];
                            $array = array();
                            if (is_array($arrayObject[$attributeName])) {
                                foreach ($arrayObject[$attributeName] as $key => $value) {
                                    $array[$key] = new $attributeType($value);
                                }
                            }
                            $this->$attributeName = $array;
                        } else {
                            try {
                                $attr = $attributeType;
                                if (!class_exists($attributeType)) {
                                    throw new ConfigMissingException("Config type [$attributeType] not found");
                                }
                                $this->$attributeName = new $attr($arrayObject[$attributeName]);
                            } catch (ConfigMissingException $e) {
                            }
                        }
                        break;
                }
            } else {
               // new ConfigMissingException("Please set up : $attributeName in your config");
            }
        }
    }

    abstract public function getAttributes();
}