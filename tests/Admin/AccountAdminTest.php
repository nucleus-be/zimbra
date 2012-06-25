<?php
use \Mockery as m;

use Symfony\Component\Validator\Validator;
use Symfony\Component\Validator\Mapping\ClassMetadataFactory;
use Symfony\Component\Validator\Mapping\Loader\StaticMethodLoader;
use Symfony\Component\Validator\ConstraintValidatorFactory;

class AccountAdminTest extends PHPUnit_Framework_TestCase
{
    public function testGetAccountReturnsAccountEntity()
    {
        $soapClient = $this->_getMockedSoapClient();
        $soapClient->getCurlClient()->shouldReceive('execute')->andReturn(file_get_contents(__DIR__.'/../_data/GetAccountResponse.xml'));

        $admin = new \Zimbra\ZCS\Admin\Account($soapClient);
        $account = $admin->getAccount('7ab4e5f5-f6a4-47bb-be18-e12b4b092a67');

        $this->assertInstanceOf('\Zimbra\ZCS\Entity\Account', $account);
        $this->assertEquals($account->getId(), '7ab4e5f5-f6a4-47bb-be18-e12b4b092a67');
    }

    public function testGetInexistingAccountThrowsException()
    {
        $this->setExpectedException('Zimbra\ZCS\Exception\Webservice');

        $soapClient = $this->_getMockedSoapClient();
        $soapClient->getCurlClient()->shouldReceive('execute')->andReturn(file_get_contents(__DIR__.'/../_data/GetAccountNotFoundResponse.xml'));

        try {
            $admin = new \Zimbra\ZCS\Admin\Account($soapClient);
            $account = $admin->getAccount('foobarbaz');
        } catch (\Zimbra\ZCS\Exception\Webservice $e) {
            $this->assertEquals($e->getMessage(), 'account.NO_SUCH_ACCOUNT');
            throw $e;
        }
    }

    public function testGetAccountListFromDomainReturnsArrayWithAccountEntities()
    {
        $soapClient = $this->_getMockedSoapClient();
        $soapClient->getCurlClient()->shouldReceive('execute')->andReturn(file_get_contents(__DIR__.'/../_data/GetAccountListByDomainResponse.xml'));

        $admin = new \Zimbra\ZCS\Admin\Account($soapClient);
        $accounts = $admin->getAccountListByDomain('nucleus.be');

        $this->assertInternalType('array', $accounts);
        $this->assertInstanceOf('\Zimbra\ZCS\Entity\Account', $accounts[0]);
    }

    public function testGetAccountListReturnsArrayWithAccountEntities()
    {
        $soapClient = $this->_getMockedSoapClient();
        $soapClient->getCurlClient()->shouldReceive('execute')->andReturn(file_get_contents(__DIR__.'/../_data/GetAccountListResponse.xml'));

        $admin = new \Zimbra\ZCS\Admin\Account($soapClient);
        $accounts = $admin->getAccountList();

        $this->assertInternalType('array', $accounts);
        $this->assertInstanceOf('\Zimbra\ZCS\Entity\Account', $accounts[0]);
    }

    public function testCreateAccount()
    {
        $responseXml = file_get_contents(__DIR__.'/../_data/CreateAccountResponse.xml');

        $soapClient = $this->_getMockedSoapClient();
        $soapClient->getCurlClient()->shouldReceive('execute')->andReturn($responseXml);

        $account = \Zimbra\ZCS\Entity\Account::createFromJson(json_decode($this->_getCreateAccountJson()));

        $admin = new \Zimbra\ZCS\Admin\Account($soapClient);
        $newAccount = $admin->createAccount($account);

        $this->assertInstanceOf('\Zimbra\ZCS\Entity\Account', $newAccount);
        $this->assertEquals($newAccount->getDisplayName(), $account->getDisplayName());
    }

    public function testCreateAccountAlreadyExistsFailsWithException()
    {
        $this->setExpectedException('Zimbra\ZCS\Exception\Webservice');

        $responseXml = file_get_contents(__DIR__.'/../_data/CreateAccountAlreadyExistsResponse.xml');

        $soapClient = $this->_getMockedSoapClient();
        $soapClient->getCurlClient()->shouldReceive('execute')->andReturn($responseXml);

        try {
            $account = \Zimbra\ZCS\Entity\Account::createFromJson(json_decode($this->_getCreateAccountJson()));
            $admin = new \Zimbra\ZCS\Admin\Account($soapClient);
            $newAccount = $admin->createAccount($account);
        } catch (\Zimbra\ZCS\Exception\Webservice $e) {
            $this->assertEquals($e->getMessage(), 'account.ACCOUNT_EXISTS');
            throw $e;
        }
    }

    public function testUpdateAccount()
    {
        $responseXml = file_get_contents(__DIR__.'/../_data/ModifyAccountResponse.xml');

        $soapClient = $this->_getMockedSoapClient();
        $soapClient->getCurlClient()->shouldReceive('execute')->andReturn($responseXml);

        /** @var $account \Zimbra\ZCS\Entity\Account */
        $account = \Zimbra\ZCS\Entity\Account::createFromJson(json_decode($this->_getUpdateAccountJson()));

        $admin = new \Zimbra\ZCS\Admin\Account($soapClient);
        $newAccount = $admin->updateAccount($account);

        $this->assertInstanceOf('\Zimbra\ZCS\Entity\Account', $newAccount);
        $this->assertEquals($newAccount->getDisplayName(), $account->getDisplayName());
        $this->assertEquals($newAccount->getMailquota(), $account->getMailquota());
    }

    public function testGetQuotaUsage()
    {
        $soapClient = $this->_getMockedSoapClient();
        $soapClient->getCurlClient()
            ->shouldReceive('execute')->andReturn(
                file_get_contents(__DIR__.'/../_data/GetAccountResponse.xml'),
                file_get_contents(__DIR__.'/../_data/GetQuotaUsageResponse.xml')
            );

        $admin = new \Zimbra\ZCS\Admin\Account($soapClient);
        $usage = $admin->getAccountQuotaUsage('8282b006-cc43-4cde-86e8-87a4cf1c5f19');

        $this->assertInternalType('array', $usage);
        $this->assertInternalType('int', $usage['limit']);
        $this->assertEquals($usage['limit'], 524288000);
        $this->assertEquals($usage['used'], 3349146);
    }

    public function testDeleteAccount()
    {
        $soapClient = $this->_getMockedSoapClient();
        $soapClient->getCurlClient()->shouldReceive('execute')->andReturn(file_get_contents(__DIR__.'/../_data/DeleteAccountResponse.xml'));

        $admin = new \Zimbra\ZCS\Admin\Account($soapClient);
        $account = $admin->deleteAccount('1accc249-d696-48a5-85d2-de8bfbf2a229');

        $this->assertTrue($account);
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

    private function _getCreateAccountJson()
    {
        return '{
            "name"          : "salesint@nucleus.be",
            "displayname"   : "Nucleus Sales Dept.",
            "password"      : "foobar",
            "accountstatus" : "active",
            "mailquota"     : 102400
        }';
    }

    private function _getUpdateAccountJson()
    {
        return '{
            "displayname"   : "Nucleus International Sales Dept.",
            "accountstatus" : "active",
            "mailquota"     : 204800
        }';
    }
}