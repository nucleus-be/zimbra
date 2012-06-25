<?php
use \Mockery as m;

use Symfony\Component\Validator\Validator;
use Symfony\Component\Validator\Mapping\ClassMetadataFactory;
use Symfony\Component\Validator\Mapping\Loader\StaticMethodLoader;
use Symfony\Component\Validator\ConstraintValidatorFactory;

class AliasAdminTest extends PHPUnit_Framework_TestCase
{
    public function testGetAliasReturnsAliasEntity()
    {
        $soapClient = $this->_getMockedSoapClient();
        $soapClient->getCurlClient()->shouldReceive('execute')->andReturn(file_get_contents(__DIR__.'/../_data/GetAliasResponse.xml'));

        $admin = new \Zimbra\ZCS\Admin\Alias($soapClient);
        $alias = $admin->getAlias('bf372ec8-c399-4249-b5e3-712436023814');

        $this->assertInstanceOf('\Zimbra\ZCS\Entity\Alias', $alias);
        $this->assertEquals($alias->getId(), 'bf372ec8-c399-4249-b5e3-712436023814');
        $this->assertEquals($alias->getName(), 'christopher2@mail.webruimte.eu');
        $this->assertEquals($alias->getTargetname(), 'chris@mail.webruimte.eu');
    }

    public function testGetInexistingAliasThrowsException()
    {
        $this->setExpectedException('Zimbra\ZCS\Exception\Webservice');

        $soapClient = $this->_getMockedSoapClient();
        $soapClient->getCurlClient()->shouldReceive('execute')->andReturn(file_get_contents(__DIR__.'/../_data/GetAliasNotFoundResponse.xml'));

        try {
            $admin = new \Zimbra\ZCS\Admin\Alias($soapClient);
            $account = $admin->getAlias('foobarbaz');
        } catch (\Zimbra\ZCS\Exception\Webservice $e) {
            $this->assertEquals($e->getMessage(), 'account.NO_SUCH_ALIAS');
            throw $e;
        }
    }

    public function testGetAliasListFromAccountReturnsArrayWithAliasEntities()
    {
        $soapClient = $this->_getMockedSoapClient();
        $soapClient->getCurlClient()->shouldReceive('execute')->andReturn(
            file_get_contents(__DIR__.'/../_data/GetAccountResponse.xml'),
            file_get_contents(__DIR__.'/../_data/GetAliasListByAccountResponse.xml')
        );

        $admin = new \Zimbra\ZCS\Admin\Alias($soapClient);
        $alias = $admin->getAliasListByAccount('7ab4e5f5-f6a4-47bb-be18-e12b4b092a67');

        $this->assertInternalType('array', $alias);
        $this->assertInstanceOf('\Zimbra\ZCS\Entity\Alias', $alias[0]);
    }

    public function testCreateAlias()
    {
        $responseXml = file_get_contents(__DIR__.'/../_data/AddAccountAliasResponse.xml');

        $soapClient = $this->_getMockedSoapClient();
        $soapClient->getCurlClient()->shouldReceive('execute')->andReturn($responseXml);

        $alias = \Zimbra\ZCS\Entity\Alias::createFromJson(json_decode($this->_getCreateAliasJson()));

        $admin = new \Zimbra\ZCS\Admin\Alias($soapClient);
        $response = $admin->createAlias($alias);

        $this->assertTrue($response);
    }

    public function testCreateAccountAlreadyExistsFailsWithException()
    {
        $this->setExpectedException('Zimbra\ZCS\Exception\Webservice');

        $responseXml = file_get_contents(__DIR__.'/../_data/AddAccountAliasAlreadyExistsResponse.xml');

        $soapClient = $this->_getMockedSoapClient();
        $soapClient->getCurlClient()->shouldReceive('execute')->andReturn($responseXml);

        try {
            $alias = \Zimbra\ZCS\Entity\Alias::createFromJson(json_decode($this->_getCreateAliasJson()));
            $admin = new \Zimbra\ZCS\Admin\Alias($soapClient);
            $admin->createAlias($alias);
        } catch (\Zimbra\ZCS\Exception\Webservice $e) {
            $this->assertEquals($e->getMessage(), 'account.ACCOUNT_EXISTS');
            throw $e;
        }
    }

    public function testDeleteAlias()
    {
        $soapClient = $this->_getMockedSoapClient();
        $soapClient->getCurlClient()->shouldReceive('execute')->andReturn(file_get_contents(__DIR__.'/../_data/RemoveAccountAliasResponse.xml'));

        // Mock the getAlias method on the AliasAdmin
        $admin = m::mock('Zimbra\ZCS\Admin\Alias[getAlias]');
        $admin->setSoapClient($soapClient);
        $admin->shouldReceive('getAlias')->andReturn($this->_getMockedAlias());

        $result = $admin->deleteAlias('c0daa6db-2a8f-4034-8496-5f452aedff47');

        $this->assertTrue($result);
    }

    private function _getMockedAlias()
    {
        $soapClient = $this->_getMockedSoapClient();
        $soapClient->getCurlClient()->shouldReceive('execute')->andReturn(file_get_contents(__DIR__.'/../_data/GetAliasResponse.xml'));

        $admin = new \Zimbra\ZCS\Admin\Alias($soapClient);
        $alias = $admin->getAlias('bf372ec8-c399-4249-b5e3-712436023814');

        return $alias;
    }

    private function _getMockedSoapClient()
    {
        $curlClient = m::mock('\Zimbra\ZCS\CurlClient');
        $curlClient->shouldReceive('setOption')->andReturn($curlClient);

        // Create a new soap client
        $soapClient = new \Zimbra\ZCS\SoapClient();
        $soapClient->setCurlClient( $curlClient );

        return $soapClient;
    }

    private function _getCreateAliasJson()
    {
        return '{
            "name"     : "chrisramakers@mail.webruimte.eu",
            "targetid" : "7ab4e5f5-f6a4-47bb-be18-e12b4b092a67"
        }';
    }

}