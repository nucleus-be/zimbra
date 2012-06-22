<?php

/**
 * A basic Zimbra object.
 *
 * @author LiberSoft <info@libersoft.it>
 * @author Chris Ramakers <chris@nucleus.be>
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Zimbra\ZCS;

use Symfony\Component\Validator\Mapping\ClassMetadata;

abstract class Entity
{
    /**
     * A validator instance
     * @var \Symfony\Component\Validator\Validator
     */
    protected $validator;

    /**
     * The Entity id
     * @var string
     */
    private $id;

    /**
     * Maps properties from the Zimbra Soap response
     * to properties of the Entity
     * @var array
     */
    protected static $_basic_datamap = array(
        'zimbraId'         => 'id'
    );

    /**
     * Extra fields to map to the Entity, same as $_basic_datamap except
     * this property should be overridden in subclasses to add extra params
     * to add to the Entity
     * @var array
     */
    protected static $_datamap = array();

    /**
     * If this entity is constructed from an XML element (probably from the SOAP webservice)
     * we store the original XML for later retieval, else we store the decoded JSON object here
     * @var \SimpleXMLElement|\stdClass XML or JSON object
     */
    protected $_source;

    /**
     * Creates a new Entity based on a json object using the datamap
     * @static
     * @param \stdClass $json
     * @return Entity
     */
    public static function createFromJson(\stdClass $json)
    {
        // Get the properties from the datamap
        $properties = array_values(self::getDataMap());

        // Create dummy entity
        $class = get_called_class();
        $entity = new $class(array());

        // Set the properties from the json based on the datamap
        foreach($properties as $property){
            $setter = 'set'.ucfirst($property);
            $entity->$setter(isset($json->$property) ? $json->$property : null);
        }

        // Set the source
        $entity->setSource($json);

        return $entity;
    }

    /**
     * Creates a new Entity based on a SimpleXML object using the datamap
     * @static
     * @param \SimpleXMLElement $xmlElement
     * @return Entity
     */
    public static function createFromXml(\SimpleXMLElement $xmlElement)
    {
        // Get the properties from the datamap
        $properties = self::getDataMap();

        // Split off the attributes
        $attributes = array();
        foreach($properties as $key => &$prop){
            $stripped = str_replace('@', '', $key);
            if($key !== $stripped) {
                $attributes[$stripped] = $prop;
                unset($properties[$key]);
            }
        }

        // Create dummy entity
        $class = get_called_class();
        $entity = new $class(array());

        // Create a new SimpleXMLElement, workaround for a bug wheren
        // simple_xml lib cannot handle XPath on elements with namespaces
        $xml = new \SimpleXMLElement($xmlElement->asXML());

        // Set the properties from the json based on the datamap
        foreach($properties as $zimbraKey => $property){
            $setter = 'set'.ucfirst($property);
            $xpathQuery = sprintf("a[@n='%s']", $zimbraKey);
            $results = $xml->xpath($xpathQuery);
            $entity->$setter(count($results) > 0 ? (string)$results[0] : null);
        }

        // Set the attributes to entity properties
        foreach($attributes as $zimbraAttribute => $attribute){
            $setter = 'set'.ucfirst($attribute);
            $entity->$setter((string)$xml->attributes()->$zimbraAttribute ?: null);
        }

        // Set the source
        $entity->setSource($xml);

        return $entity;
    }

    /**
     * Validated this Entity according to the rules specified in self::loadValidatorMetadata
     *
     * @param null $groups
     * @throws Exception\InvalidEntity
     * @throws Exception
     * @return \Symfony\Component\Validator\ConstraintViolationList
     */
    public function validate($groups = null)
    {
        if(!$this->validator){
            throw new \Zimbra\ZCS\Exception('Cannot validate Entity, no validator present!');
        }

        $violations = $this->validator->validate($this, $groups);
        if(count($violations) > 0) {
            throw new \Zimbra\ZCS\Exception\InvalidEntity($violations);
        }

        return $violations;
    }

    /**
     * String representation of this Entity
     * @return string
     */
    public function __toString()
    {
        return in_array('name', $this->getDataMap()) ? $this->getName() : $this->getId();
    }

    /**
     * Array representation of this Entity
     * @return array
     */
    public function toArray()
    {
        $properties = array_values(self::getDataMap());
        $array = array();

        foreach($properties as $property){
            $getter = 'get'.ucfirst($property);
            $array[$property] = $this->$getter();
        }

        return $array;
    }

    /**
     * Returns an array representation of this Entity that can be
     * used to communicate with the Zimbra ZCS server, the array keys
     * used are all zimbra property names or attributes
     * @return array
     */
    public function toPropertyArray()
    {
        $array = $this->toArray();
        $properties = $this->getDataMap();
        $propertyArray = array();

        foreach($properties as $zimbraProperty => $property)
        {
            if(key_exists($property, $array)){
                $propertyArray[$zimbraProperty] = $array[$property];
            }
        }

        return $propertyArray;
    }

    /**
     * Sets the XML this Entity was built from
     * @param \SimpleXMLElement|\stdClass $source
     * @return Entity
     */
    public function setSource($source)
    {
        $this->_source = $source;
        return $this;
    }

    /**
     * Returns the source this Entity was built from
     * @return \SimpleXMLElement|\stdClass
     */
    public function getSource()
    {
        return $this->_source;
    }

    /**
     * Returns a merged datamap
     * @static
     * @return array
     */
    public static function getDataMap()
    {
        return array_merge(static::$_basic_datamap, static::$_datamap);
    }

    /**
     * Setter for the ID property
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Getter for the ID property
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $validator
     * @return \Zimbra\ZCS\Entity
     */
    public function setValidator(\Symfony\Component\Validator\Validator $validator)
    {
        $this->validator = $validator;
        return $this;
    }

}
