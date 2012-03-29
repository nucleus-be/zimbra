<?php
namespace App\Rest\Response\Writer;
use \App\Rest\Response\Writer,
    \App\Util\Array2Xml;

class Xml extends Writer implements WriterInterface
{
    protected $headers = array('Content-Type' => 'application/xml');

    public function output()
    {
        return self::arrayToXml($this->data);
    }

    public static function arrayToXml($data)
    {
        return Array2Xml::createXML('response', $data)->saveXML();
    }

}