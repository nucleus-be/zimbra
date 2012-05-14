<?php
use \Mockery as m;

use Symfony\Component\Validator\Validator;
use Symfony\Component\Validator\Mapping\ClassMetadataFactory;
use Symfony\Component\Validator\Mapping\Loader\StaticMethodLoader;
use Symfony\Component\Validator\ConstraintValidatorFactory;

class CosEntityTest extends PHPUnit_Framework_TestCase
{

    public function testValidateCosNameNotEmpty()
    {
        try {
            $name = "";
            $jsonData = $this->_getCosJson($name);

            /** @var $cos \Zimbra\ZCS\Entity\Domain */
            $cos = \Zimbra\ZCS\Entity\Cos::createFromJson($jsonData);
            $cos->setValidator($this->_getValidator());
            $cos->validate();
        } catch (\Exception $e) {
            $this->assertEquals('Zimbra\ZCS\Exception\InvalidEntity', get_class($e));
            $errors = $e->getErrors();
            $this->assertEquals($errors[0]['property'], 'name');
            $this->assertEquals($errors[0]['errormessage'], "This value should not be blank. (received value: '')");
        }
    }

    public function testValidateCosNameNotNull()
    {
        try {
            $jsonData = $this->_getCosJson();
            $cos = \Zimbra\ZCS\Entity\Cos::createFromJson($jsonData);
            $cos->setValidator($this->_getValidator());
            $cos->setName(null);
            $cos->validate();
        } catch (\Exception $e) {
            $this->assertEquals('Zimbra\ZCS\Exception\InvalidEntity', get_class($e));
            $errors = $e->getErrors();
            $this->assertEquals($errors[0]['property'], 'name');
            $this->assertEquals($errors[0]['errormessage'], "This value should not be null. (received value: NULL)");
        }
    }

    public function testCreateCosEntityFromXml()
    {
        $xmlString = $this->_getCosXml();
        $xmlData   = new \SimpleXMLElement($xmlString);
        $cosXml    = $xmlData->children('soap', true)->Body->children()->GetCosResponse->children();

        /** @var $cos \Zimbra\ZCS\Entity\Domain */
        $cos = \Zimbra\ZCS\Entity\Cos::createFromXml($cosXml[0]);

        $this->assertEquals($cos->getName(), "bronze");
        $this->assertEquals($cos->getId(), "a8f379c0-6a0e-48bf-98c7-3e7facb294d3");
    }

    public function testCreateCosEntityFromJson()
    {
        $name = "foobar";

        $jsonData = $this->_getCosJson($name);

        /** @var $cos \Zimbra\ZCS\Entity\Cos */
        $cos = \Zimbra\ZCS\Entity\Cos::createFromJson($jsonData);

        $this->assertEquals($cos->getName(), $name);
    }

    public function _getCosXml()
    {
        return file_get_contents(realpath(__DIR__.'/../_data/').'/GetCosResponse.xml');
    }

    private function _getCosJson($name = 'foobar')
    {
        $jsonString = '{"name":"'.$name.'"}';
        return json_decode($jsonString);
    }

    private function _getValidator()
    {
        return new Validator(
            new ClassMetadataFactory(new StaticMethodLoader()),
            new ConstraintValidatorFactory()
        );
    }
}