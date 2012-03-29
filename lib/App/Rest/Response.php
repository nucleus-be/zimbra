<?php
namespace App\Rest;

class Response extends \Symfony\Component\HttpFoundation\Response
{
    /**
     * The application data
     * @var array
     */
    public $data = array();

    /**
     * Flag to indicate wether or not this is an error response
     * @var bool
     */
    public $error = false;

    /**
     * An optional error message that is outputted by a Response Writer
     * @var string
     */
    public $errorMessage;

    /**
     * Constructor overrides default behaviour by inspecting $content, if it's an
     * array the $content variable is used to set the Application Data on the Response
     * so we can later process it
     *
     * @param string $content
     * @param int $status
     * @param array $headers
     */
    public function __construct($content = '', $status = 200, $headers = array())
    {
        if(is_array($content)){
            $this->setData($content);
            $content = "Application data";
        }
        parent::__construct($content, $status, $headers);
    }

    /**
     * Setter for $data
     * @param array $value
     * @return \App\Rest\Response
     */
    public function setData(array $value)
    {
        $this->data = $value;
        return $this;
    }

    /**
     * Adds an entry to the data array with the given key
     * @param $key
     * @param $value
     * @return \App\Rest\Response
     */
    public function addData($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Getter for $data
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * This method sets $error to true and sets the specified
     * statuscode on the Response object
     *
     * @param $statusCode
     * @return \App\Rest\Response
     */
    public function setError($statusCode)
    {
        $this->setStatusCode($statusCode);
        $this->error = true;
        return $this;
    }

    /**
     * Returns true in case the Response is an error response
     *
     * @return bool
     */
    public function isError()
    {
        return $this->error === true;
    }

    /**
     * Sets a specific Error message on the response object and also
     * flags the Response as an Error response
     *
     * @param string $message
     * @return \App\Rest\Response
     */
    public function setErrorMessage($message)
    {
        $this->error = true;
        $this->errorMessage = $message;
        return $this;
    }

    /**
     * Getter for the error message
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }
}