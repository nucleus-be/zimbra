<?php
use \Mockery as m;

use Symfony\Component\Validator\Validator;
use Symfony\Component\Validator\Mapping\ClassMetadataFactory;
use Symfony\Component\Validator\Mapping\Loader\StaticMethodLoader;
use Symfony\Component\Validator\ConstraintValidatorFactory;

class EntityTest extends PHPUnit_Framework_TestCase
{
    public function testValidateThrowsExceptionWhenNoValidatorPresent()
    {
        try{
            $jsonString = '{"name":"foobar"}';
            $jsonData   = json_decode($jsonString);

            /** @var $domain \Zimbra\ZCS\Entity\Domain */
            $domain = \Zimbra\ZCS\Entity\Domain::createFromJson($jsonData);
            $domain->validate();
        } catch (\Exception $e) {
            $this->assertEquals('Zimbra\ZCS\Exception', get_class($e));
            $this->assertEquals('Cannot validate Entity, no validator present!', $e->getMessage());
        }
    }

    public function testValidationSuccessReturnsConstraintViolationList()
    {
        $jsonString = '{"name":"foobar"}';
        $jsonData   = json_decode($jsonString);

        /** @var $domain \Zimbra\ZCS\Entity\Domain */
        $domain = \Zimbra\ZCS\Entity\Domain::createFromJson($jsonData);

        $validator = new Validator(
            new ClassMetadataFactory(new StaticMethodLoader()),
            new ConstraintValidatorFactory()
        );
        $domain->setValidator($validator);
        $result = $domain->validate();

        $this->assertInstanceOf('Symfony\Component\Validator\ConstraintViolationList', $result);
        $this->assertEquals(sizeof($result), 0);
    }

    public function testGetSourceEqualsSource()
    {
        $jsonString = '{"name":"foobar"}'; // Json without the cos id
        $jsonData   = json_decode($jsonString);

        /** @var $domain \Zimbra\ZCS\Entity\Domain */
        $domain = \Zimbra\ZCS\Entity\Domain::createFromJson($jsonData);

        $this->assertEquals($jsonData, $domain->getSource());
    }

    public function testToStringOutputsName()
    {
        $name = "foobarbaz";
        $jsonString = '{"name":"'.$name.'"}'; // Json without the cos id
        $jsonData   = json_decode($jsonString);

        /** @var $domain \Zimbra\ZCS\Entity\Domain */
        $domain = \Zimbra\ZCS\Entity\Domain::createFromJson($jsonData);

        $this->assertEquals($name, (string)$domain);
    }

    public function testToArray()
    {
        $jsonData = $this->_getDomainJson('foo', 'bar');
        $domain = \Zimbra\ZCS\Entity\Domain::createFromJson($jsonData);

        $array = $domain->toArray();

        $this->assertInternalType('array', $array);
        $this->assertEquals(4,     sizeof($array));
        $this->assertEquals(null,  $array['id']);
        $this->assertEquals('foo', $array['name']);
        $this->assertEquals('bar', $array['defaultCosId']);
    }

    public function testToPropertyArray()
    {
        $jsonData = $this->_getDomainJson('foo', 'bar');
        $domain = \Zimbra\ZCS\Entity\Domain::createFromJson($jsonData);

        $array = $domain->toPropertyArray();

        $this->assertInternalType('array', $array);
        $this->assertEquals(4,     sizeof($array));
        $this->assertEquals(null,  $array['zimbraId']);
        $this->assertEquals('foo', $array['zimbraDomainName']);
        $this->assertEquals('bar', $array['zimbraDomainDefaultCOSId']);
    }

    public function testDomainFromJsonWithoutNameValidates()
    {
        $jsonData = json_decode('{"defaultCosId" : "a8f379c0-6a0e-48bf-98c7-3e7facb294d3"}');

        $domain = \Zimbra\ZCS\Entity\Domain::createFromJson($jsonData);
        $domain->setValidator(new Validator(
            new ClassMetadataFactory(new StaticMethodLoader()),
            new ConstraintValidatorFactory()
        ));

        $result = $domain->validate();
        $this->assertEquals($result->count(), 0);
    }

    private function _getDomainJson($name = 'foobar', $defaultCosId = 'fizzbuzz')
    {
        $jsonString = '{"name":"'.$name.'","defaultCosId":"'.$defaultCosId.'"}';
        return json_decode($jsonString);
    }

}
