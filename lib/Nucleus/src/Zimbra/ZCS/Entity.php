<?php

/**
 * A basic Zimbra object.
 *
 * @author LiberSoft <info@libersoft.it>
 * @author Chris Ramakers <chris.ramakers@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Zimbra\ZCS;

class Entity
{
    /**
     * Maps properties from the Zimbra Soap response
     * to properties of the Entity
     * @var array
     */
    protected $_basic_datamap = array(
        'zimbraId'         => 'id'
    );

    /**
     * Extra fields to map to the Entity, same as $_basic_datamap except
     * this property should be overridden in subclasses to add extra params
     * to add to the Entity
     * @var array
     */
    protected $_datamap = array();

    /**
     * Stores the properties of this entity, accessible by the magic
     * __get and __set methods
     * @var array
     */
    protected $_data = array();

    /**
     * If this entity is constructed from an XML element (probably from the SOAP webservice)
     * we store the original XML for later retieval
     * @var \SimpleXMLElement
     */
    protected $_sourceXml;

    /**
     * This variable contains all parsed XML properties from the SOAP webservice, most
     * of these properties aren't used but they are here for retrieval anyway
     * @var array
     */
    protected $_extra_data;

    /**
     * Constructor
     * @param $data Either an array with parameters or a SimpleXMLElement
     */
    public function __construct($data)
    {
        // Save the originating XML for later access and
        // parse it into an array saved as $_extra_data
        if($data instanceof \SimpleXMLElement){
            $this->setSourceXml($data);
            $this->_setExtraDataFromXml($data);
        }

        // Process the datamap to pull XML properties into this Entity
        $this->_processDataMap();
    }

    /**
     * Processes the datamap and sets properties on this Entity
     * based on the mappings
     */
    protected function _processDataMap()
    {
        $datamap = $this->getDataMap();
        $data = $this->getExtraData();
        foreach($datamap as $zimbra_key => $local_key){
            if(isset($data[$zimbra_key]) && !is_null($data[$zimbra_key])){
               $this->_data[$local_key] = (string) $data[$zimbra_key];
            } else {
                $this->_data[$local_key] = null;
            }
        }
    }

    /**
     * Converts a SimpleXMLElement to an array with all properties
     * returned from the SOAP webservice
     * @param \SimpleXMLElement $xml
     */
    protected function _setExtraDataFromXml(\SimpleXMLElement $xml)
    {
        foreach ($xml->children()->a as $data) {
            $key = (string) $data['n'];

            switch ($data) {
                case 'FALSE':
                    $this->_extra_data[$key] = false;
                    break;
                case 'TRUE':
                    $this->_extra_data[$key] = true;
                    break;
                default:
                    if(array_key_exists($key, $this->_extra_data)){
                        $this->_extra_data[$key] = (array)$this->_extra_data[$key];
                        $this->_extra_data[$key][] = (string) $data;
                    } else {
                        $this->_extra_data[$key] = (string) $data;
                    }
            }
        }
    }

    /**
     * String representation of this Entity
     * @return string
     */
    public function __toString()
    {
        return (string)$this->_data['name'];
    }

    /**
     * Magic getter for $_data
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->_data)) {
            return $this->_data[$name];
        } elseif(array_key_exists($name, $this->_extra_data)){
            return $this->_extra_data[$name];
        }
    }

    /**
     * Getter for all data in $_data
     * @param $name
     * @return mixed
     */
    public function get($name)
    {
        return $this->__get($name);
    }

    /**
     * Magic setter for $_data
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->_data[$name] = $value;
    }

    /**
     * Setter for $_data
     * @param $name
     * @param $value
     */
    public function set($name, $value)
    {
        $this->__set($name, $value);
    }

    /**
     * Array representation of this Entity
     * @return array
     */
    public function toArray()
    {
        return $this->_data;
    }

    /**
     * Gets all attributes set on this Entity
     * @return array
     */
    public function getAttributes()
    {
        return array_keys($this->_data);
    }

    /**
     * Sets the XML this Entity was built from
     * @param \SimpleXMLElement $sourceXml
     * @return Entity
     */
    public function setSourceXml(\SimpleXMLElement $sourceXml)
    {
        $this->_sourceXml = $sourceXml;
        return $this;
    }

    /**
     * Returns the XML this Entity was built from
     * @return \SimpleXMLElement
     */
    public function getSourceXml()
    {
        return $this->_sourceXml;
    }

    /**
     * Returns a merged datamap
     * @return array
     */
    public function getDataMap()
    {
        return array_merge($this->_basic_datamap, $this->_datamap);
    }

    /**
     * Gets all data returned from the SOAP webservice for this entity as
     * an array
     * @return array
     */
    public function getExtraData()
    {
        return $this->_extra_data;
    }

}
