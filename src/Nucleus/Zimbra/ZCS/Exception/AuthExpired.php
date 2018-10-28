<?php

/**
 * This Exception is thrown when the Zimbra webservice returns
 * a service.AUTH_EXPIRED error, meaning the token must be refreshed.
 *
 * @license http://www.gnu.org/licenses/gpl.txt
 */
namespace Zimbra\ZCS\Exception;

class AuthExpired extends \Zimbra\ZCS\Exception
{
}
