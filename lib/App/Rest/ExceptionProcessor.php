<?php
namespace App\Rest;
use App\Rest;

class ExceptionProcessor
{
    public static function process(\Exception $e)
    {
        $response = new Response();
        switch(get_class($e)){
            case "App\Rest\Exception\AccessDenied":
                return $response->setError(403)->setErrorMessage($e->getMessage() ?: "Access denied");
                break;
            case "App\Rest\Exception\ResourceNotFound":
                return $response->setError(404)->setErrorMessage($e->getMessage() ?: "Resource not found");
                break;
            case "Zimbra\ZCS\Entity\InvalidException":
                return $response->setError(500)->setErrorMessage($e->getMessage() ?: "Unknown error")->setErrors($e->getViolations());
            case "Exception":
            default:
                return $response->setError(500)->setErrorMessage($e->getMessage() ?: "Unknown error");
        }
    }
}