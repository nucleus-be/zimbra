<?php
namespace App\Rest\Response;

class Writer
{
    /**
     * This variable holds the actual data that is going to be outputted by the writer
     * @var array
     */
    protected $data = array();

    /**
     * Key-value pairs of HTTP headers that will be sent when this Writer is used
     * in the REST webservice
     * @var array
     */
    protected $headers = array();

    /**
     * Factory method to return the proper Writer based
     * on the $format provided
     *
     * @static
     * @param string $format
     * @return Writer\Json|Writer\Xml
     */
    public static function factory($format = 'json')
    {
        switch(strtolower($format)){
            case 'xml':
                return new Writer\Xml();
            case 'json':
            default:
                return new Writer\Json();
        }
    }

    /**
     * Getter for the $headers property
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Sets the $data property
     * @param array $data
     * @return \App\Rest\Response\Writer
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Getter for the $data property
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}