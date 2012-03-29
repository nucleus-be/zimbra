<?php
namespace App\Rest\Response\Writer;
use App\Rest\Response\Writer;

class Json extends Writer implements WriterInterface
{
    protected $headers = array('Content-Type' => 'application/json');

    public function output()
    {
        return json_encode($this->data);
    }
}