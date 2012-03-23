<?php
namespace App\Rest\Response\Writer;
use App\Rest\Response\Writer;

class Json extends Writer
{
    public $data;

    public $headers = array('Content-Type' => 'application/json');

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
        return json_encode($this->data);
    }

    public function getHeaders()
    {
        return $this->headers;
    }
}