<?php

/**
 * Handles the assembling of the low-level XML SOAP message
 *
 * @author LiberSoft <info@libersoft.it>
 * @author Chris Ramakers <chris@nucleus.be>
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Zimbra\ZCS;

class SoapClient
{

    /**
     * The XML message that is going to be sent to the Soap Server
     * @var \SimpleXMLElement
     */
    private $message;

    /**
     * Pointer to the context element from the $message
     * @var \SimpleXMLElement
     */
    private $context;

    /**
     * The curl client
     * @var \Zimbra\ZCS\CurlClient
     */
    private $curlClient;

    /**
     * @var null|string
     */
    private $username;

    /**
     * @var null|string
     */
    private $password;

    /**
     * When true all XML will be outputted
     * @var bool
     */
    static public $debug = false;

    /**
     * Constructor which initializes the connection to the receiving server
     * @param string $server
     * @param integer $port
     * @param string $username
     * @param string $password
     */
    public function __construct($server = null, $port = null, $username = null, $password = null)
    {
        // @codeCoverageIgnoreStart
        if($server && $port) {
            $curlClient = new \Zimbra\ZCS\CurlClient("https://$server:$port/service/admin/soap");
            $this->setCurlClient($curlClient);
        }
        // @codeCoverageIgnoreEnd

        $this->message = new \SimpleXMLElement('<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope"></soap:Envelope>');
        $this->context = $this->message->addChild('Header')->addChild('context', null, 'urn:zimbra');
        $this->message->addChild('Body');

        // @codeCoverageIgnoreStart
        if($username && $password && $this->getCurlClient()){
            $this->username = $username;
            $this->password = $password;
            $this->auth($username, $password);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Setter for the curl client
     * @param \Zimbra\ZCS\CurlClient $curlClient
     * @return SoapClient
     */
    public function setCurlClient(\Zimbra\ZCS\CurlClient $curlClient)
    {
        $curlClient
                ->setOption(CURLOPT_POST, TRUE)
                ->setOption(CURLOPT_RETURNTRANSFER, TRUE)
                ->setOption(CURLOPT_SSL_VERIFYPEER, FALSE)
                ->setOption(CURLOPT_SSL_VERIFYHOST, FALSE)
                ->setOption(CURLOPT_CONNECTTIMEOUT, 30);

        $this->curlClient = $curlClient;
        return $this;
    }

    /**
     * Getter for the curl client
     * @return CurlClient
     */
    public function getCurlClient()
    {
        return $this->curlClient;
    }

    /**
     * Authenticate
     * @param string $username
     * @param string $password
     * @return string The authtoken received when logging in
     */
    public function auth($username, $password)
    {
        $xml = $this->request('AuthRequest', array('name' => $username, 'password' => $password));
        $authToken = $xml->children()->AuthResponse->authToken;
        $this->addContextChild('authToken', $authToken);
        return (string) $authToken;
    }

    /**
     * Returns the complete message as an XML string
     * @return string
     */
    public function getXml()
    {
        return $this->message->asXml();
    }

    /**
     * Sets a value on the context node of the XML request
     * @param $name Tagname for the context node
     * @param $value Tag value for the node
     */
    public function addContextChild($name, $value)
    {
        if (isset($this->context->$name)) {
            $this->context->$name = $value;
        } else {
            $this->context->addChild($name, $value);
        }
    }

    /**
     * Getter for the context element
     * @return \SimpleXMLElement
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Sends an XML request to the SOAP server
     * @param string $action The action you are performing, a soap method defined in the wsdl
     * @param array $attributes The attributes for the XML node that defines the action
     * @param array $params Request params
     * @throws Exception\Soap
     * @return \SimpleXMLElement The response's Body tag
     */
    public function request($action, $attributes = array(), $params = array())
    {
        // Sanity check to see if we have connected
        if (!$this->getCurlClient()){
            throw new \Zimbra\ZCS\Exception\Soap('No valid connection has been established, have you connected and authenticated with the ZCS Soap webservice?');
        }

        unset($this->message->children('soap', true)->Body);
        $body = $this->message->addChild('Body');
        $actionChild = $body->addChild($action, null, 'urn:zimbraAdmin');

        foreach ($attributes as $key => $value) {
            $actionChild->addAttribute($key, $value);
        }

        foreach ($params as $key => $value) {
            if (is_array($value)) {
                switch ($key) {
                    case 'attributes':
                        foreach ($value as $l => $b) {
                            if(is_bool($b)){
                                $b = ($b === true) ? 'TRUE' : 'FALSE';
                            }
                            $newParam = $actionChild->addChild('a', $b);
                            $newParam->addAttribute('n', $l);
                        }
                        break;
                    default:
                        $newParam = $actionChild->addChild($key, $value['_']);
                        unset($value['_']);
                        foreach ($value as $l => $b) {
                            if(is_bool($b)){
                                $b = ($b === true) ? 'TRUE' : 'FALSE';
                            }
                            $newParam->addAttribute($l, $b);
                        }
                }
            } else {
                $actionChild->addChild($key, $value);
            }
        }

        if(self::$debug === true){
            echo PHP_EOL.PHP_EOL."## REQUEST".PHP_EOL;
            echo self::formatXml($this->getXml());
        }

        $this->getCurlClient()->setOption(CURLOPT_POSTFIELDS, $this->getXml());
        $reply = $this->getCurlClient()->execute();
        try {
            return $this->handleResponse($reply);
        } catch(Exception\AuthExpired $exception) {
            // try to re-authenticate to refresh token
            $this->auth($this->username, $this->password);
            return $this->handleResponse($reply);
        }
    }

    /**
     * Handles the response
     * @param string $soapMessage The response
     * @throws Exception\AuthExpired
     * @throws Exception\EntityNotFound
     * @throws Exception\Soap
     * @throws Exception\Webservice
     * @return \SimpleXMLElement The response XML <Body> tag
     */
    private function handleResponse($soapMessage)
    {
        // No message is returned, something went wrong, throw a Soap exception which
        // means there was an error communicating with the soap webservice`
        if (!$soapMessage) {
            throw new \Zimbra\ZCS\Exception\Soap($this->getCurlClient()->getError(), $this->getCurlClient()->getErrorNr());
        }

        // Construct a SimpleXMLElement from the message
        $xml = new \SimpleXMLElement($soapMessage);

        if(self::$debug === true){
            echo PHP_EOL.PHP_EOL."## RESPONSE".PHP_EOL;
            echo self::formatXml($xml->asXml());
        }

        // If the response is a Fault throw a webservice exception
        $fault = $xml->children('soap', true)->Body->Fault;
        if ($fault) {
            throw self::getExceptionForFault($fault->Detail->children()->Error->Code->__toString());
        }

        // Return the body element from the XML
        return $xml->children('soap', true)->Body;
    }

    /**
     * Outputs a human readable version of the XML passed
     * @static
     * @param string $xml
     * @param bool $escape True to HTML escape the output
     * @return string
     */
    public static function formatXml($xml, $escape = false)
    {
        $dom = new \DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        @$dom->loadXML($xml);
        $output = $dom->saveXML();
        return $escape ? htmlentities($output, ENT_QUOTES, 'utf-8') : $output;
    }

    public static function getExceptionForFault($faultMessage)
    {
        switch($faultMessage) {
            case 'account.NO_SUCH_DOMAIN':
                $exception = new \Zimbra\ZCS\Exception\EntityNotFound(
                    'Domain cannot be found',
                    \Zimbra\ZCS\Exception\EntityNotFound::ERR_DOMAIN_NOT_FOUND
                );
                break;
            case 'account.NO_SUCH_ACCOUNT':
                $exception = new \Zimbra\ZCS\Exception\EntityNotFound(
                    'Account cannot be found',
                    \Zimbra\ZCS\Exception\EntityNotFound::ERR_ACCOUNT_NOT_FOUND
                );
                break;
            case 'account.NO_SUCH_ALIAS':
                $exception = new \Zimbra\ZCS\Exception\EntityNotFound(
                    'Alias cannot be found',
                    \Zimbra\ZCS\Exception\EntityNotFound::ERR_ALIAS_NOT_FOUND
                );
                break;
            case 'account.NO_SUCH_COS':
                $exception = new \Zimbra\ZCS\Exception\EntityNotFound(
                    'Cos cannot be found',
                    \Zimbra\ZCS\Exception\EntityNotFound::ERR_COS_NOT_FOUND
                );
                break;
            case 'service.AUTH_EXPIRED':
                $exception = new \Zimbra\ZCS\Exception\AuthExpired(
                    'Authentication token expired'
                );
                break;
            default:
                $exception = new \Zimbra\ZCS\Exception\Webservice($faultMessage);
        }

        return $exception;
    }

}
