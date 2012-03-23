<?php
namespace App\Rest;

class Response extends \Symfony\Component\HttpFoundation\Response
{
    public $data = array();
    public $error = false;
    public $errorMessage;

    public function setData($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }

    public function getData($key)
    {
        return $this->data[$key] ?: null;
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