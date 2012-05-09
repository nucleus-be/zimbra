<?php

/**
 * The exception is thrown when an error communicating with the soap webservice occurs
 * When an error response from the webservice is returned on the other hand
 * a \Zimbra\ZCS\Exception\Webservice exception will be thrown!
 *
 * @author Chris Ramakers <chris.ramakers@gmail.com>
 */
namespace Zimbra\ZCS\Exception;

class Soap extends \Zimbra\ZCS\Exception
{

}
