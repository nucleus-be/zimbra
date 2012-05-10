<?php
use \Mockery as m;

class SoapClientTest extends PHPUnit_Framework_TestCase
{
    public function testFailedAuthThrowsWebserviceException()
    {
        // Mock the curl library
        $curlClient = $this->_getCurlClientMock();
        $curlClient->shouldReceive('execute')->andReturn($this->_getAuthFailedXml());

        // Create a new soap client
        $soapClient = new \Zimbra\ZCS\SoapClient();
        $soapClient->setCurlClient( $curlClient );

        // Set that we expect an exception to be raised
        $this->setExpectedException('Zimbra\ZCS\Exception\Webservice');

        // Fire the request
        $soapClient->auth('foo', 'bar');
    }

    public function testFailedAuthThrowsWebserviceExceptionWithRightMessage()
    {
        // Mock the curl library
        $curlClient = $this->_getCurlClientMock();
        $curlClient->shouldReceive('execute')->andReturn($this->_getAuthFailedXml());

        // Create a new soap client
        $soapClient = new \Zimbra\ZCS\SoapClient();
        $soapClient->setCurlClient( $curlClient );

        try {
            // Fire the request
            $soapClient->auth('foo', 'bar');
        } catch (\Exception $e) {
            $this->assertEquals('account.AUTH_FAILED', $e->getMessage());
        }
    }

    public function testContextNodeIsSetAfterSuccesfullAuth()
    {
        // Mock the curl library
        $curlClient = $this->_getCurlClientMock();
        $curlClient->shouldReceive('execute')->andReturn($this->_getAuthSuccessXml());

        // Create a new soap client
        $soapClient = new \Zimbra\ZCS\SoapClient();
        $soapClient->setCurlClient( $curlClient );

        // Execute the auth request
        $soapClient->auth('foo', 'bar');

        // Check if the right context element is set for consecutive requests
        $context = $soapClient->getContext();
        $this->assertEquals(
            '0_1c1c96a3c39aa5771c14a954b386d45e0da24be9_69643d33363a31386662303831662d386663642d343834332d616239372d6135663465653937666339303b6578703d31333a313333363639383637373436383b61646d696e3d313a313b76763d313a313b747970653d363a7a696d6272613b',
            (string)$context->authToken
        );
    }

    public function testThrowsSoapExceptionWhenNoCurlClientSet()
    {
        // Create a soapclient without a CurlClient
        $soapClient = new \Zimbra\ZCS\SoapClient();

        // Set the expection type we are expecting to be thrown
        $this->setExpectedException('Zimbra\ZCS\Exception\Soap');

        // Fire the request
        $soapClient->auth('foo', 'bar');
    }

    public function testThrowsSoapExceptionWhenEmptyResponse()
    {
        // Mock the curl library
        $curlClient = $this->_getCurlClientMock();
        $curlClient->shouldReceive('execute')->andReturn('') // Return empty string
                   ->shouldReceive('getError')
                   ->shouldReceive('getErrorNr');

        // Create a new soap client
        $soapClient = new \Zimbra\ZCS\SoapClient();
        $soapClient->setCurlClient( $curlClient );

        // Set the expection type we are expecting to be thrown
        $this->setExpectedException('Zimbra\ZCS\Exception');

        $soapClient->auth('foo', 'bar');
    }

    public function _getCurlClientMock()
    {
        $curlClient = m::mock('\Zimbra\ZCS\CurlClient');
        $curlClient->shouldReceive('setOption')->andReturn($curlClient);
        return $curlClient;
    }

    public function _getAuthFailedXml()
    {
        return <<<EOT
<?xml version="1.0"?>
<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope">
  <soap:Header>
    <context xmlns="urn:zimbra"/>
  </soap:Header>
  <soap:Body>
    <soap:Fault>
      <soap:Code>
        <soap:Value>soap:Sender</soap:Value>
      </soap:Code>
      <soap:Reason>
        <soap:Text>authentication failed for [admin]</soap:Text>
      </soap:Reason>
      <soap:Detail>
        <Error xmlns="urn:zimbra">
          <Code>account.AUTH_FAILED</Code>
          <Trace>btpool0-14://mail.webruimte.eu:7071/service/admin/soap:1336654190637:40a62659afaeacf2</Trace>
        </Error>
      </soap:Detail>
    </soap:Fault>
  </soap:Body>
</soap:Envelope>
EOT;
    }

    public function _getAuthSuccessXml()
    {
        return <<<EOT
<?xml version="1.0"?>
<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope">
  <soap:Header>
    <context xmlns="urn:zimbra">
      <change token="9836"/>
    </context>
  </soap:Header>
  <soap:Body>
    <AuthResponse xmlns="urn:zimbraAdmin">
      <authToken>0_1c1c96a3c39aa5771c14a954b386d45e0da24be9_69643d33363a31386662303831662d386663642d343834332d616239372d6135663465653937666339303b6578703d31333a313333363639383637373436383b61646d696e3d313a313b76763d313a313b747970653d363a7a696d6272613b</authToken>
      <lifetime>43200000</lifetime>
      <a n="zimbraIsDomainAdminAccount">false</a>
    </AuthResponse>
  </soap:Body>
</soap:Envelope>
EOT;
    }
}