<?php

/**
 * @author Chris Ramakers <chris.ramakers@gmail.com>
 */
namespace Zimbra\ZCS\Exception;

class InvalidEntity extends \Zimbra\ZCS\Exception
{
    protected $violations;

    public function __construct($violations, $message="Something went wrong validating this Entity!", $code=0, $previous=null)
    {
        $this->violations = $violations;
        parent::__construct($message, $code, $previous);
    }

    public function getErrors()
    {
        $violations = array();
        foreach($this->violations as $violation){
            $property = $violation->getPropertyPath();
            $violations[] = array(
                'property' => $property,
                'errormessage' => sprintf("%s (received value: %s)", $violation->getMessage(), var_export($violation->getInvalidValue(), true))
            );
        }
        return $violations;
    }
}