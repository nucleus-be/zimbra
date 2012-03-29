<?php
namespace App\Rest\Response;

class Writer
{
    public static function factory($format)
    {
        switch(strtolower($format)){
            case 'xml':
                return new Writer\Xml();
            case 'yaml':
                return new Writer\Yaml();
            case 'json':
            default:
                return new Writer\Json();
        }
    }
}