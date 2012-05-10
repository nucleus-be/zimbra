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
     * @param \Zimbra\ZCS\SoapClient $client
     */
    public function __construct(\Zimbra\ZCS\SoapClient $client)
    {
        $this->setSoapClient($client);
    }

    /**
     * The setter for the Soap Client class
     * @param \Zimbra\ZCS\SoapClient $soapClient
     * @return \Zimbra\ZCS\Admin
     */
    public function setSoapClient($soapClient)
    {
        $this->soapClient = $soapClient;
        return $this;
    }

    /**
     * @return \Zimbra\ZCS\SoapClient
     */
    public function getSoapClient()
    {
        return $this->soapClient;
    }

}
