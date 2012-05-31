<?php
use \Mockery as m;

class AdminTest extends PHPUnit_Framework_TestCase
{
    public function testAdminAcceptsSoapClient()
    {
        // Mock the curl library
        $curlClient = m::mock('\Zimbra\ZCS\CurlClient');
        $curlClient->shouldReceive('setOption')->andReturn($curlClient);

        // Create a new soap client
        $soapClient = new \Zimbra\ZCS\SoapClient();
        $soapClient->setCurlClient( $curlClient );

        // Create an admin class
        $admin = new \Zimbra\ZCS\Admin\Account($soapClient);

        // Test if the soap client is set
        $this->assertInstanceOf('\Zimbra\ZCS\SoapClient', $admin->getSoapClient());
    }
}