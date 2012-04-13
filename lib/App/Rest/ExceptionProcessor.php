<?php
namespace App\Rest;
use App\Rest;

class ExceptionProcessor
{
    public static function process(\Exception $e)
    {
        $response = new Response();
        switch(get_class($e)){
            case "App\Rest\Exception\ResourceNotFound":
                $response->setError(404);
                break;
            case "Zimbra\ZCS\Entity\InvalidException":
                $response->setError(500)->setErrors($e->getViolations());
                break;
            case "Zimbra\ZCS\Exception":
                $response->setError(500)->setErrorCode($e->getZimbraErrorCode());
                break;
            case "Exception":
            default:
                $response->setError(500);
        }
        return $response->setErrorMessage($e->getMessage());
    }
}