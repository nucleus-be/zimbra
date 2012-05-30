<?php
use \Mockery as m;

use Symfony\Component\Validator\Validator;
use Symfony\Component\Validator\Mapping\ClassMetadataFactory;
use Symfony\Component\Validator\Mapping\Loader\StaticMethodLoader;
use Symfony\Component\Validator\ConstraintValidatorFactory;

class DomainAdminTest extends PHPUnit_Framework_TestCase
{
    public function testGetDomainReturnsDomainEntity()
    {
        $soapClient = $this->_getMockedSoapClient();
        $soapClient->getCurlClient()->shouldReceive('execute')->andReturn(file_get_contents(__DIR__.'/../_data/GetDomainResponse.xml'));

        $admin = new \Zimbra\ZCS\Admin\Domain($soapClient);
        $domain = $admin->getDomain('d60c6cbc-6c53-456e-ad3d-3b75117cbc64');

        $this->assertInstanceOf('\Zimbra\ZCS\Entity\Domain', $domain);
        $this->assertEquals($domain->getId(), 'd60c6cbc-6c53-456e-ad3d-3b75117cbc64');
    }

    public function testGetInexistingDomainThrowsException()
    {
        $this->setExpectedException('Zimbra\ZCS\Exception\Webservice');

        $soapClient = $this->_getMockedSoapClient();
        $soapClient->getCurlClient()->shouldReceive('execute')->andReturn(file_get_contents(__DIR__.'/../_data/GetDomainNotFoundResponse.xml'));

        try {
            $admin = new \Zimbra\ZCS\Admin\Domain($soapClient);
            $domain = $admin->getDomain('foobarbaz');
        } catch (\Zimbra\ZCS\Exception\Webservice $e) {
            $this->assertEquals($e->getMessage(), 'account.NO_SUCH_DOMAIN');
            throw $e;
        }
    }

    public function testGetDomainsReturnsArrayWithDomainEntities()
    {
        $soapClient = $this->_getMockedSoapClient();
        $soapClient->getCurlClient()->shouldReceive('execute')->andReturn(file_get_contents(__DIR__.'/../_data/GetAllDomainsResponse.xml'));

        $admin = new \Zimbra\ZCS\Admin\Domain($soapClient);
        $accounts = $admin->getDomains();

        $this->assertInternalType('array', $accounts);
        $this->assertInstanceOf('\Zimbra\ZCS\Entity\Domain', $accounts[0]);
    }

    public function testCreateDomain()
    {
        $responseXml = file_get_contents(__DIR__.'/../_data/CreateDomainResponse.xml');

        $soapClient = $this->_getMockedSoapClient();
        $soapClient->getCurlClient()->shouldReceive('execute')->andReturn($responseXml);

        $domain = \Zimbra\ZCS\Entity\Domain::createFromJson(json_decode($this->_getCreateDomainJson()));

        $admin = new \Zimbra\ZCS\Admin\Domain($soapClient);
        $newDomain = $admin->createDomain($domain);

        $this->assertInstanceOf('\Zimbra\ZCS\Entity\Domain', $newDomain);
        $this->assertEquals($newDomain->getName(), $domain->getName());
        $this->assertEquals($newDomain->getDefaultCosId(), $domain->getDefaultCosId());
    }

    public function testCreateDomainAlreadyExistsFailsWithException()
    {
        $this->setExpectedException('Zimbra\ZCS\Exception\Webservice');

        $responseXml = file_get_contents(__DIR__.'/../_data/CreateDomainAlreadyExistsResponse.xml');

        $soapClient = $this->_getMockedSoapClient();
        $soapClient->getCurlClient()->shouldReceive('execute')->andReturn($responseXml);

        try {
            $domain = \Zimbra\ZCS\Entity\Domain::createFromJson(json_decode($this->_getCreateDomainJson()));
            $admin = new \Zimbra\ZCS\Admin\Domain($soapClient);
            $newDomain = $admin->createDomain($domain);
        } catch (\Zimbra\ZCS\Exception\Webservice $e) {
            $this->assertEquals($e->getMessage(), 'account.DOMAIN_EXISTS');
            throw $e;
        }
    }

    public function testUpdateDomain()
    {
        $responseXml = file_get_contents(__DIR__.'/../_data/ModifyDomainResponse.xml');

        $soapClient = $this->_getMockedSoapClient();
        $soapClient->getCurlClient()->shouldReceive('execute')->andReturn($responseXml);

        $domain = \Zimbra\ZCS\Entity\Domain::createFromJson(json_decode($this->_getUpdateDomainJson()));

        $admin = new \Zimbra\ZCS\Admin\Domain($soapClient);
        $newDomain = $admin->updateDomain($domain);

        $this->assertInstanceOf('\Zimbra\ZCS\Entity\Domain', $newDomain);
        $this->assertEquals($newDomain->getDefaultCosId(), $domain->getDefaultCosId());
    }

    public function testDeleteDomain()
    {
        $soapClient = $this->_getMockedSoapClient();
        $soapClient->getCurlClient()->shouldReceive('execute')->andReturn(file_get_contents(__DIR__.'/../_data/DeleteDomainResponse.xml'));

        $admin = new \Zimbra\ZCS\Admin\Domain($soapClient);
        $response = $admin->deleteDomain('225e58d1-367a-4337-af00-2acbc2307a59');

        $this->assertTrue($response);
    }

    public function testDeleteNotEmptyDomainThrowsException()
    {
        $this->setExpectedException("Zimbra\ZCS\Exception\Webservice");

        $soapClient = $this->_getMockedSoapClient();
        $soapClient->getCurlClient()->shouldReceive('execute')->andReturn(file_get_contents(__DIR__.'/../_data/DeleteDomainNotEmptyResponse.xml'));

        try {
            $admin = new \Zimbra\ZCS\Admin\Domain($soapClient);
            $response = $admin->deleteDomain('9f61f68f-fcd2-460e-8098-de01d250b5df');
        } catch (\Zimbra\ZCS\Exception\Webservice $e) {
            $this->assertEquals($e->getMessage(), 'account.DOMAIN_NOT_EMPTY');
            throw $e;
        }

        $this->assertTrue($response);
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

    private function _getCreateDomainJson()
    {
        return '{
           "name"         : "domain.com",
           "defaultCosId" : "a8f379c0-6a0e-48bf-98c7-3e7facb294d3"
        }';
    }

    private function _getUpdateDomainJson()
    {
        return '{
           "name"         : "domain.com",
           "defaultCosId" : "e00428a1-0c00-11d9-836a-000d93afea2a"
        }';
    }
}