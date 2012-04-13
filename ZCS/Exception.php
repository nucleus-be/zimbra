<?php

/**
 * @author LiberSoft <info@libersoft.it>
 * @author Chris Ramakers <chris.ramakers@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.txt
 */
namespace Zimbra\ZCS;

class Exception extends \Exception
{
    /**
     * The error code returned by zimbra
     * @var string
     */
    public $zimbraErrorCode;

    /**
     * Mapping Zimbra errors to errorcodes and messages
     * @var array
     */
    public static $errorMap = array(
        'default' => array(
            'message' => 'An unexpected error has occurred: %s'
        ),
        // Account errors
        'account.ACCOUNT_EXISTS' => array(
            'message' => 'This account already exists'
        ),
        'account.NO_SUCH_DOMAIN' => array(
            'message' => 'This domain does not exist'
        ),
        'account.DISTRIBUTION_LIST_EXISTS' => array(
            'message' => 'This distribution list already exists'
        ),
        'account.DOMAIN_EXISTSs' => array(
            'message' => 'This domain already exists'
        ),
        'account.DOMAIN_NOT_EMPTY' => array(
            'message' => 'This domain is not empty, it still contains other entities'
        ),
    );

    /**
     * Constructor
     * @param $zimbraErrorCode
     */
    public function __construct($zimbraErrorCode)
    {
        $this->setZimbraErrorCode($zimbraErrorCode);
        $error = self::getError($zimbraErrorCode);
        parent::__construct($error['message']);
    }

    private static function getError($zimbraErrorCode)
    {
        if(array_key_exists($zimbraErrorCode, self::$errorMap)){
            $error = self::$errorMap[$zimbraErrorCode];
        } else {
            $error = self::$errorMap['default'];
            $error['message'] = sprintf($error['message'], $zimbraErrorCode);
        }

        return $error;
    }

    /**
     * Setter for the zimbra error code
     * @param string $zimbraErrorCode
     */
    public function setZimbraErrorCode($zimbraErrorCode)
    {
        $this->zimbraErrorCode = $zimbraErrorCode;
    }

    /**
     * Getter for the zimbra error code
     * @return string
     */
    public function getZimbraErrorCode()
    {
        return $this->zimbraErrorCode;
    }

}
