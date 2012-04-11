<?php
/**
 * Zimbra SOAP API calls.
 *
 * @author Chris Ramakers <chris.ramakers@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.txt
 */
namespace Zimbra\ZCS\Admin;

class Account
    extends \Zimbra\ZCS\Admin
{

    /**
     * True to exclude all system accounts
     * @var bool
     */
    private $excludeSystemAccounts = true;

    /**
     * Regular expressions to identify system users that should be
     * excluded
     * @var array
     */
    private $systemUsersRegexp = array(
        "admin",
        "postmaster",
        "ham.*",
        "spam.*",
        "virus-quarantine.*",
        "galsync"
    );

}
