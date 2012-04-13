<?php

/**
 * @author Chris Ramakers <chris.ramakers@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.txt
 */
namespace Zimbra\ZCS\Entity;

class InvalidException extends \Exception
{
    protected $violations;

    public function __construct($violations, $message="Something went wrong validating this Entity!", $code=null)
    {
        $this->violations = $violations;
        parent::__construct($message, $code);
    }

    public function getViolations()
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