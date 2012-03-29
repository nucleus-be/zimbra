<?php

namespace App\Rest\Response\Writer;

interface WriterInterface
{
    public function setData($data);
    public function getData();
    public function output();
    public function getHeaders();
}