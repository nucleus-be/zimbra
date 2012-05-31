<?php
use \Mockery as m;

use Symfony\Component\Validator\Validator;
use Symfony\Component\Validator\Mapping\ClassMetadataFactory;
use Symfony\Component\Validator\Mapping\Loader\StaticMethodLoader;
use Symfony\Component\Validator\ConstraintValidatorFactory;

class AccountEntityTest extends PHPUnit_Framework_TestCase
{

    public function testValidate()
    {
        // TODO: test other validation rules!
    }

    public function testValidateCreateAccountNameNotEmpty()
    {
        $this->setExpectedException("\Zimbra\ZCS\Exception\InvalidEntity");

        try {
            $jsonData = $this->_getAccountJson();

            /** @var $account \Zimbra\ZCS\Entity\Account */
            $account = \Zimbra\ZCS\Entity\Account::createFromJson($jsonData);
            $account->setName('');
            $account->setValidator($this->_getValidator());
            $account->validate('create');
            $this->fail('Validation passed, it should fail instead!');
        } catch (\Zimbra\ZCS\Exception\InvalidEntity $e) {
            $errors = $e->getErrors();
            $this->assertEquals($errors[0]['property'], 'name');
            $this->assertEquals($errors[0]['errormessage'], "This value should not be blank. (received value: '')");
            throw $e;
        }

    }

    public function testValidateCreateAccountPasswordNotEmpty()
    {
        $this->setExpectedException("\Zimbra\ZCS\Exception\InvalidEntity");

        try {
            $jsonData = $this->_getAccountJson();

            /** @var $account \Zimbra\ZCS\Entity\Account */
            $account = \Zimbra\ZCS\Entity\Account::createFromJson($jsonData);
            $account->setPassword('');
            $account->setValidator($this->_getValidator());
            $account->validate('create');
            $this->fail('Validation passed, it should fail instead!');
        } catch (\Zimbra\ZCS\Exception\InvalidEntity $e) {
            $errors = $e->getErrors();
            $this->assertEquals($errors[0]['property'], 'password');
            $this->assertEquals($errors[0]['errormessage'], "This value should not be blank. (received value: '')");
            throw $e;
        }

    }

    public function testValidateCreateAccountNameNotLongerThan64Chars()
    {
        $this->markTestSkipped('Not testable due to symfony bug #4398 (https://github.com/symfony/symfony/issues/4398)');

        $this->setExpectedException("\Zimbra\ZCS\Exception\InvalidEntity");

        try {
            $jsonData = $this->_getAccountJson();
            $invalidName = str_repeat('foobar', 20);

            /** @var $account \Zimbra\ZCS\Entity\Account */
            $account = \Zimbra\ZCS\Entity\Account::createFromJson($jsonData);
            $account->setName($invalidName);
            $account->setValidator($this->_getValidator());
            $account->validate('create');
            $this->fail('Validation passed, it should fail instead!');
        } catch (\Zimbra\ZCS\Exception\InvalidEntity $e) {
            $errors = $e->getErrors();
            $this->assertEquals($errors[0]['property'], 'name');
            $this->assertEquals($errors[0]['errormessage'], "This value is too long. It should have 64 characters or less. (received value: '$invalidName')");
            throw $e;
        }

    }

    public function testValidateCreateAccountNameNotNull()
    {
        $this->setExpectedException("\Zimbra\ZCS\Exception\InvalidEntity");

        try {
            $jsonData = $this->_getAccountJson();

            /** @var $account \Zimbra\ZCS\Entity\Account */
            $account = \Zimbra\ZCS\Entity\Account::createFromJson($jsonData);
            $account->setValidator($this->_getValidator());
            $account->setName(null);
            $account->validate('create');
            $this->fail('Validation passed, it should fail instead!');
        } catch (\Zimbra\ZCS\Exception\InvalidEntity $e) {
            $errors = $e->getErrors();
            $this->assertEquals($errors[0]['property'], 'name');
            $this->assertEquals($errors[0]['errormessage'], "This value should not be null. (received value: NULL)");
            throw $e;
        }
    }

    public function testValidateCreateAccountPasswordNotNull()
    {
        $this->setExpectedException("\Zimbra\ZCS\Exception\InvalidEntity");

        try {
            $jsonData = $this->_getAccountJson();

            /** @var $account \Zimbra\ZCS\Entity\Account */
            $account = \Zimbra\ZCS\Entity\Account::createFromJson($jsonData);
            $account->setValidator($this->_getValidator());
            $account->setPassword(null);
            $account->validate('create');
            $this->fail('Validation passed, it should fail instead!');
        } catch (\Zimbra\ZCS\Exception\InvalidEntity $e) {
            $errors = $e->getErrors();
            $this->assertEquals($errors[0]['property'], 'password');
            $this->assertEquals($errors[0]['errormessage'], "This value should not be null. (received value: NULL)");
            throw $e;
        }
    }

    public function testCreateAccountEntityFromXml()
    {
        $xmlString  = $this->_getAccountXml();
        $xmlData    = new \SimpleXMLElement($xmlString);
        $accountXml = $xmlData->children('soap', true)->Body->children()->GetAccountResponse->children();

        /** @var $account \Zimbra\ZCS\Entity\Account */
        $account = \Zimbra\ZCS\Entity\Account::createFromXml($accountXml[0]);

        $this->assertEquals($account->getName(), "chris@mail.webruimte.eu");
        $this->assertEquals($account->getId(), "7ab4e5f5-f6a4-47bb-be18-e12b4b092a67");
        $this->assertEquals($account->getPassword(), "VALUE-BLOCKED");
        $this->assertEquals($account->getAccountstatus(), "active");
        $this->assertEquals($account->getMailquota(), "52428800");
        $this->assertEquals($account->getHost(), 'mail.webruimte.eu');
        $this->assertEquals($account->getUsername(), 'chris');
    }

    public function testCreateAccountEntityFromJson()
    {
        $name = "foo@bar.com";
        $displayname = "Foo Bar";
        $password = "foobarbazfizzbuzz!";
        $accountstatus = "active";
        $mailquota = 52428800;

        $jsonData = $this->_getAccountJson($name, $displayname, $password, $accountstatus, $mailquota);

        /** @var $account \Zimbra\ZCS\Entity\Account */
        $account = \Zimbra\ZCS\Entity\Account::createFromJson($jsonData);

        $this->assertEquals($account->getName(), $name);
        $this->assertEquals($account->getDisplayname(), $displayname);
        $this->assertEquals($account->getPassword(), $password);
        $this->assertEquals($account->getAccountstatus(), $accountstatus);
        $this->assertEquals($account->getMailquota(), $mailquota);
    }

    public function _getAccountXml()
    {
        return file_get_contents(realpath(__DIR__.'/../_data/').'/GetAccountResponse.xml');
    }

    private function _getAccountJson($name = 'foobar@fizzbuzz.com', $displayname = "foobar", $password = "fizzbuzz", $accountstatus = "active", $mailquota = 512)
    {
        $jsonString = '{
            "name"          : "'.$name.'",
            "displayname"   : "'.$displayname.'",
            "password"      : "'.$password.'",
            "accountstatus" : "'.$accountstatus.'",
            "mailquota"     : '.$mailquota.'
        }';
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