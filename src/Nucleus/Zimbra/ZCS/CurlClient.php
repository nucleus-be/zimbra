<?php
/**
 * Curl client abstraction class, only point of this class
 * is to allow mockery in the unit tests, and a little clearer API for curl.
 *
 * @author Chris Ramakers <chris@nucleus.be>
 * @license http://www.gnu.org/licenses/gpl.txt
 */
namespace Zimbra\ZCS;

class CurlClient
{
    private $handle = null;

    /**
     * Constructor
     * @param string $url
     */
    public function __construct($url) {
        $this->handle = curl_init($url);
    }

    /**
     * Sets option on the curl resources
     * @param string $name
     * @param string $value
     * @return \Zimbra\ZCS\CurlClient
     */
    public function setOption($name, $value) {
        curl_setopt($this->handle, $name, $value);
        return $this;
    }

    /**
     * Executes the curl request
     * @return mixed
     */
    public function execute() {
        return curl_exec($this->handle);
    }

    /**
     * Gets info from the curl resource
     * @param string $name
     * @return mixed
     */
    public function getInfo($name) {
        return curl_getinfo($this->handle, $name);
    }

    /**
     * Closes the curl resource
     * @return \Zimbra\ZCS\CurlClient
     */
    public function close() {
        curl_close($this->handle);
        return $this;
    }

    /**
     * Gets the curl error message
     * @return string
     */
    public function getError()
    {
        return curl_error($this->handle);
    }

    /**
     * Gets the curl error number
     * @return string
     */
    public function getErrorNr()
    {
        return curl_errno($this->handle);
    }
}