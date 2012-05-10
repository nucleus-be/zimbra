<?php
/**
 * Zimbra SOAP API calls.
 *
 * @author LiberSoft <info@libersoft.it>
 * @author Chris Ramakers <chris@nucleus.be>
 * @license http://www.gnu.org/licenses/gpl.txt
 */
namespace Zimbra\ZCS;

abstract class Admin
{

    /**
     * The soapclient
     * @var SoapClient
     */
    protected $soapClient;

    /**
     * Constructor
     * @param string $server The hostname or IP of the zimbra server
     * @param int $port The port to connect to the server (defaults to 7071)
     */
    public function __construct($server, $port = 7071)
    {
        $this->soapClient = new \Zimbra\ZCS\SoapClient($server, $port);
    }

    /**
     * Authenticate
     * @param string $username
     * @param string $password
     * @return string The authtoken received when logging in
     */
    public function auth($username, $password)
    {
        $xml = $this->soapClient->request('AuthRequest', array('name' => $username, 'password' => $password));

        $authToken = $xml->children()->AuthResponse->authToken;
        $this->soapClient->addContextChild('authToken', $authToken);

        return (string) $authToken;
    }

}
