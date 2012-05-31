<?php
use \Mockery as m;

use Symfony\Component\Validator\Validator;
use Symfony\Component\Validator\Mapping\ClassMetadataFactory;
use Symfony\Component\Validator\Mapping\Loader\StaticMethodLoader;
use Symfony\Component\Validator\ConstraintValidatorFactory;

class DomainEntityTest extends PHPUnit_Framework_TestCase
{

    public function testValidateDomainNameNotEmpty()
    {
        try {
            $name = "";
            $defaultCosId = "fizzbuzz";
            $jsonData = $this->_getDomainJson($name, $defaultCosId);

            /** @var $domain \Zimbra\ZCS\Entity\Domain */
            $domain = \Zimbra\ZCS\Entity\Domain::createFromJson($jsonData);
            $domain->setValidator($this->_getValidator());
            $domain->validate();
        } catch (\Exception $e) {
            $this->assertEquals('Zimbra\ZCS\Exception\InvalidEntity', get_class($e));
            $errors = $e->getErrors();
            $this->assertEquals($errors[0]['property'], 'name');
            $this->assertEquals($errors[0]['errormessage'], "This value should not be blank. (received value: '')");
        }
    }

    public function testValidateDomainNameNotNull()
    {
        try {
            $jsonData = $this->_getDomainJson();
            $domain = \Zimbra\ZCS\Entity\Domain::createFromJson($jsonData);
            $domain->setValidator($this->_getValidator());
            $domain->setName(null);
            $domain->validate();
        } catch (\Exception $e) {
            $this->assertEquals('Zimbra\ZCS\Exception\InvalidEntity', get_class($e));
            $errors = $e->getErrors();
            $this->assertEquals($errors[0]['property'], 'name');
            $this->assertEquals($errors[0]['errormessage'], "This value should not be null. (received value: NULL)");
        }
    }

    public function testValidateDomainDefaultCosIdNoEmptyString()
    {
        try {
            $name = "foobar";
            $defaultCosId = "";
            $jsonData = $this->_getDomainJson($name, $defaultCosId);

            /** @var $domain \Zimbra\ZCS\Entity\Domain */
            $domain = \Zimbra\ZCS\Entity\Domain::createFromJson($jsonData);
            $domain->setValidator($this->_getValidator());
            $domain->validate();
        } catch (\Exception $e) {
            $this->assertEquals('Zimbra\ZCS\Exception\InvalidEntity', get_class($e));
            $errors = $e->getErrors();
            $this->assertEquals($errors[0]['property'], 'defaultCosId');
            $this->assertEquals($errors[0]['errormessage'], "defaultCosId must be null or a valid Cos ID (received value: '')");
        }
    }

    public function testDefaultCosIdIsNullWhenOmmitted()
    {
        $jsonString = '{"name":"foobar"}'; // Json without the cos id
        $jsonData   = json_decode($jsonString);

        /** @var $domain \Zimbra\ZCS\Entity\Domain */
        $domain = \Zimbra\ZCS\Entity\Domain::createFromJson($jsonData);

        $this->assertEquals(null, $domain->getDefaultCosId());
    }

    public function testCreateDomainEntityFromXml()
    {
        $xmlString = $this->_getDomainXml();
        $xmlData   = new \SimpleXMLElement($xmlString);
        $domainXml = $xmlData->children('soap', true)->Body->children()->GetDomainResponse->children();

        /** @var $domain \Zimbra\ZCS\Entity\Domain */
        $domain = \Zimbra\ZCS\Entity\Domain::createFromXml($domainXml[0]);

        $this->assertEquals($domain->getName(), "chris.be");
        $this->assertEquals($domain->getDefaultCosId(), "a8f379c0-6a0e-48bf-98c7-3e7facb294d3");
        $this->assertEquals($domain->getId(), "d60c6cbc-6c53-456e-ad3d-3b75117cbc64");
    }

    public function testCreateDomainEntityFromJson()
    {
        $name = "foobar";
        $defaultCosId = "fizzbuzz";

        $jsonData = $this->_getDomainJson($name, $defaultCosId);

        /** @var $domain \Zimbra\ZCS\Entity\Domain */
        $domain = \Zimbra\ZCS\Entity\Domain::createFromJson($jsonData);

        $this->assertEquals($domain->getName(), $name);
        $this->assertEquals($domain->getDefaultCosId(), $defaultCosId);
    }

    public function _getDomainXml()
    {
        return file_get_contents(realpath(__DIR__.'/../_data/').'/GetDomainResponse.xml');
    }

    private function _getDomainJson($name = 'foobar', $defaultCosId = 'fizzbuzz')
    {
        $jsonString = '{"name":"'.$name.'","defaultCosId":"'.$defaultCosId.'"}';
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