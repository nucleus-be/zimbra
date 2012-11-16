<?php
use \Mockery as m;

use Symfony\Component\Validator\Validator;
use Symfony\Component\Validator\Mapping\ClassMetadataFactory;
use Symfony\Component\Validator\Mapping\Loader\StaticMethodLoader;
use Symfony\Component\Validator\ConstraintValidatorFactory;

class CosAdminTest extends PHPUnit_Framework_TestCase
{
    public function testGetCosReturnsCosEntity()
    {
        $soapClient = $this->_getMockedSoapClient();
        $soapClient->getCurlClient()->shouldReceive('execute')->andReturn(file_get_contents(__DIR__.'/../_data/GetCosResponse.xml'));

        $admin = new \Zimbra\ZCS\Admin\Cos($soapClient);
        $cos = $admin->getCos('a8f379c0-6a0e-48bf-98c7-3e7facb294d3');

        $this->assertInstanceOf('\Zimbra\ZCS\Entity\Cos', $cos);
        $this->assertEquals($cos->getId(), 'a8f379c0-6a0e-48bf-98c7-3e7facb294d3');
    }

    public function testGetInexistingCosThrowsException()
    {
        $this->setExpectedException('Zimbra\ZCS\Exception\EntityNotFound');

        $soapClient = $this->_getMockedSoapClient();
        $soapClient->getCurlClient()->shouldReceive('execute')->andReturn(file_get_contents(__DIR__.'/../_data/GetCosNotFoundResponse.xml'));

        try {
            $admin = new \Zimbra\ZCS\Admin\Cos($soapClient);
            $cos = $admin->getCos('foobarbaz');
        } catch (\Zimbra\ZCS\Exception\Webservice $e) {
            $this->assertEquals($e->getMessage(), 'account.NO_SUCH_COS');
            throw $e;
        }
    }

    public function testGetCosListReturnsArrayWithCosEntities()
    {
        $soapClient = $this->_getMockedSoapClient();
        $soapClient->getCurlClient()->shouldReceive('execute')->andReturn(file_get_contents(__DIR__.'/../_data/GetAllCosResponse.xml'));

        $admin = new \Zimbra\ZCS\Admin\Cos($soapClient);
        $cos = $admin->getCosList();

        $this->assertInternalType('array', $cos);
        $this->assertInstanceOf('\Zimbra\ZCS\Entity\Cos', $cos[0]);
    }

    public function _getMockedSoapClient()
    {
        $curlClient = m::mock('\Zimbra\ZCS\CurlClient');
        $curlClient->shouldReceive('setOption')->andReturn($curlClient);

        // Create a new soap client
        $soapClient = new \Zimbra\ZCS\SoapClient();
        $soapClient->setCurlClient( $curlClient );

        return $soapClient;
    }

}
