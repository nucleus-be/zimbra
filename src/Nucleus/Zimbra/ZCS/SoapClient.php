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
     * curl resource handle
     * @var resource
     */
    private $curlHandle;

    /**
     * When true all XML will be outputted
     * @var bool
     */
    static $debug = false;

    /**
     * Constructor which initializes the connection to the receiving server
     * @param string $server
     * @param integer $port
     */
    public function __construct($server, $port)
    {
        $this->curlHandle = curl_init();
        curl_setopt($this->curlHandle, CURLOPT_URL, "https://$server:$port/service/admin/soap");
        curl_setopt($this->curlHandle, CURLOPT_POST, TRUE);
        curl_setopt($this->curlHandle, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($this->curlHandle, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($this->curlHandle, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($this->curlHandle, CURLOPT_CONNECTTIMEOUT, 30);

        $this->message = new \SimpleXMLElement('<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope"></soap:Envelope>');
        $this->context = $this->message->addChild('Header')->addChild('context', null, 'urn:zimbra');
        $this->message->addChild('Body');
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
     * Sends an XML request to the SOAP server
     * @param string $action The action you are performing, a soap method defined in the wsdl
     * @param array $attributes The attributes for the XML node that defines the action
     * @param array $params Request params
     * @return \SimpleXMLElement The response's Body tag
     */
    public function request($action, $attributes = array(), $params = array())
    {
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
                            $newParam = $actionChild->addChild('a', $b);
                            $newParam->addAttribute('n', $l);
                        }
                        break;
                    default:
                        $newParam = $actionChild->addChild($key, $value['_']);
                        unset($value['_']);
                        foreach ($value as $l => $b) {
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

        curl_setopt($this->curlHandle, CURLOPT_POSTFIELDS, $this->getXml());
        return $this->handleResponse(curl_exec($this->curlHandle));
    }

    /**
     * Handles the response
     * @param string $soapMessage The response
     * @return \SimpleXMLElement The response XML <Body> tag
     */
    private function handleResponse($soapMessage)
    {
        // No message is returned, something went wrong, throw a Soap exception which
        // means there was an error communicating with the soap webservice
        if (!$soapMessage) {
            throw new \Zimbra\ZCS\Exception\Soap(curl_error($this->curlHandle), curl_errno($this->curlHandle));
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
            throw new \Zimbra\ZCS\Exception\Webservice($fault->Detail->children()->Error->Code->__toString());
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

}
