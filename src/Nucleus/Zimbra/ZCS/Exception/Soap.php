<?php

/**
 * The exception is thrown when an error communicating with the soap webservice occurs
 * When an error response from the webservice is returned on the other hand
 * a \Zimbra\ZCS\Exception\Webservice exception will be thrown!
 *
 *
 * @author Chris Ramakers <chris@nucleus.be>
 * @license http://www.gnu.org/licenses/gpl.txt
 */
namespace Zimbra\ZCS\Exception;

class Soap extends \Zimbra\ZCS\Exception
{

}
