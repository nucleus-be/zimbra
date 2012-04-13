<?php
namespace App\Rest\Exception;

class ResourceNotFound extends \Exception
{
    public function __construct($message='', $code=0, $previous=null)
    {
        parent::__construct($message ?: "Requested resource not found", $code, $previous);
    }
}