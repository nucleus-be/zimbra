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
            'message' => 'An unexpected error has occurred',
            'code'    => 10000
        ),
        'account.ACCOUNT_EXISTS' => array(
            'message' => 'This account already exists',
            'code'    => 20001
        ),
        'account.NO_SUCH_DOMAIN' => array(
            'message' => 'This domain does not exist',
            'code'    => 20002
        ),
        'account.DISTRIBUTION_LIST_EXISTS' => array(
            'message' => 'This distribution list already exists',
            'code'    => 20003
        ),
        'account.DOMAIN_EXISTS' => array(
            'message' => 'This domain already exists',
            'code'    => 20004
        ),
        'service.PROXY_ERROR' => array(
            'message' => 'Error while proxying request to target server',
            'code'    => 30001
        ),
    );

    /**
     * Constructor
     * @param $zimbraErrorCode
     */
    public function __construct($zimbraErrorCode)
    {
        $this->zimbraErrorCode = $zimbraErrorCode;
        $error = self::getError($zimbraErrorCode);
        parent::__construct($error['message'], $error['code']);
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

}
