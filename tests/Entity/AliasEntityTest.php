<?php
use \Mockery as m;

use Symfony\Component\Validator\Validator;
use Symfony\Component\Validator\Mapping\ClassMetadataFactory;
use Symfony\Component\Validator\Mapping\Loader\StaticMethodLoader;
use Symfony\Component\Validator\ConstraintValidatorFactory;

class AliasEntityTest extends PHPUnit_Framework_TestCase
{

    public function testValidateAliasNameNotEmpty()
    {
        try {
            $name = "";
            $targetId = "bf372ec8-c399-4249-b5e3-712436023814";
            $jsonData = $this->_getAliasJson($name, $targetId);

            /** @var $alias \Zimbra\ZCS\Entity\Alias */
            $alias = \Zimbra\ZCS\Entity\Alias::createFromJson($jsonData);
            $alias->setValidator($this->_getValidator());
            $alias->validate();
        } catch (\Exception $e) {
            $this->assertEquals('Zimbra\ZCS\Exception\InvalidEntity', get_class($e));
            $errors = $e->getErrors();
            $this->assertEquals($errors[0]['property'], 'name');
            $this->assertEquals($errors[0]['errormessage'], "This value should not be blank. (received value: '')");
        }
    }

    public function testValidateAliasNameNotNull()
    {
        try {
            $jsonData = $this->_getAliasJson();
            $alias = \Zimbra\ZCS\Entity\Alias::createFromJson($jsonData);
            $alias->setValidator($this->_getValidator());
            $alias->setName(null);
            $alias->validate();
        } catch (\Exception $e) {
            $this->assertEquals('Zimbra\ZCS\Exception\InvalidEntity', get_class($e));
            $errors = $e->getErrors();
            $this->assertEquals($errors[0]['property'], 'name');
            $this->assertEquals($errors[0]['errormessage'], "This value should not be null. (received value: NULL)");
        }
    }

    public function testValidateAliasTargetIdNotEmpty()
    {
        try {
            $name = "foo";
            $targetId = "";
            $jsonData = $this->_getAliasJson($name, $targetId);

            /** @var $alias \Zimbra\ZCS\Entity\Alias */
            $alias = \Zimbra\ZCS\Entity\Alias::createFromJson($jsonData);
            $alias->setValidator($this->_getValidator());
            $alias->validate();
        } catch (\Exception $e) {
            $this->assertEquals('Zimbra\ZCS\Exception\InvalidEntity', get_class($e));
            $errors = $e->getErrors();
            $this->assertEquals($errors[0]['property'], 'targetid');
            $this->assertEquals($errors[0]['errormessage'], "This value should not be blank. (received value: '')");
        }
    }

    public function testValidateAliasTargetIdNotNull()
    {
        try {
            $jsonData = $this->_getAliasJson();
            $alias = \Zimbra\ZCS\Entity\Alias::createFromJson($jsonData);
            $alias->setValidator($this->_getValidator());
            $alias->setTargetid(null);
            $alias->validate();
        } catch (\Exception $e) {
            $this->assertEquals('Zimbra\ZCS\Exception\InvalidEntity', get_class($e));
            $errors = $e->getErrors();
            $this->assertEquals($errors[0]['property'], 'targetid');
            $this->assertEquals($errors[0]['errormessage'], "This value should not be null. (received value: NULL)");
        }
    }

    public function testCreateAliasEntityFromXml()
    {
        $xmlString = $this->_getAliasXml();
        $xmlData   = new \SimpleXMLElement($xmlString);
        $aliasXml    = $xmlData->children('soap', true)->Body->children()->SearchDirectoryResponse->children();

        /** @var $alias \Zimbra\ZCS\Entity\Alias */
        $alias = \Zimbra\ZCS\Entity\Alias::createFromXml($aliasXml[0]);

        $this->assertEquals($alias->getName(), "christopher2@mail.webruimte.eu");
        $this->assertEquals($alias->getId(), "bf372ec8-c399-4249-b5e3-712436023814");
        $this->assertEquals($alias->getTargetid(), "7ab4e5f5-f6a4-47bb-be18-e12b4b092a67");
        $this->assertEquals($alias->getTargetname(), "chris@mail.webruimte.eu");
        $this->assertEquals($alias->getUid(), "christopher2");
    }

    public function testCreateAliasEntityFromJson()
    {
        $name = "foo@bar.com";
        $targetId = "7ab4e5f5-f6a4-47bb-be18-e12b4b092a67";

        $jsonData = $this->_getAliasJson($name, $targetId);

        /** @var $alias \Zimbra\ZCS\Entity\Alias */
        $alias = \Zimbra\ZCS\Entity\Alias::createFromJson($jsonData);

        $this->assertEquals($alias->getName(), $name);
        $this->assertEquals($alias->getTargetid(), $targetId);
    }

    public function _getAliasXml()
    {
        return file_get_contents(realpath(__DIR__.'/../_data/').'/GetAliasResponse.xml');
    }

    private function _getAliasJson($name = 'foobar@fizzbuzz.com', $targetId = "7ab4e5f5-f6a4-47bb-be18-e12b4b092a67")
    {
        $jsonString = '{"name":"'.$name.'", "targetid" : "'.$targetId.'"}';
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