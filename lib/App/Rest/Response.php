<?php
namespace App\Rest;

class Response extends \Symfony\Component\HttpFoundation\Response
{
    public $data = array();
    public $error = false;
    public $errorMessage;

    public function __construct($content = '', $status = 200, $headers = array())
    {
        if(is_array($content)){
            $this->setData($content);
            $content = "Application data";
        }
        parent::__construct($content, $status, $headers);
    }

    public function setData(array $value)
    {
        $this->data = $value;
        return $this;
    }

    public function addData($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setError($statusCode)
    {
        $this->setStatusCode($statusCode);
        $this->error = true;
        return $this;
    }

    public function isError()
    {
        return $this->error === true;
    }

    public function setErrorMessage($message)
    {
        $this->errorMessage = $message;
        return $this;
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }
}