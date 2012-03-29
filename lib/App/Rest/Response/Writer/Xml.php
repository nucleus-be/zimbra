<?php
namespace App\Rest\Response\Writer;
use App\Rest\Response\Writer;

class Xml extends Writer implements WriterInterface
{
    public $data;

    public $headers = array('Content-Type' => 'application/xml');

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function output()
    {
        $xml = new \SimpleXMLElement('<response/>');
        array_walk_recursive($this->data, function($value, $key) use ($xml){
            $xml->addChild($key, $value);
        });
        return $xml->asXml();
    }

    public function getHeaders()
    {
        return $this->headers;
    }
}