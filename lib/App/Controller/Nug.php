<?php
namespace App\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use App\Rest;

class Nug
    extends \App\Controller
    implements ControllerProviderInterface
{
    /**
     * The nug service class
     * @var \App\Service\Nug
     */
    public $nugService;

    /**
     * Returns routes to connect to the given application.
     *
     * @param \Silex\Application $app
     * @return \Silex\ControllerCollection
     */
    public function connect(Application $app)
    {
        $this->init($app);

        $this->nugService = $this->app['service_nug'];

        // Domain actions
        $this->controllers->get   ('/domain/',                     array($this, '_domainGetCollection'));
        $this->controllers->post  ('/domain/',                     array($this, '_domainCreate'));
        $this->controllers->get   ('/domain/{domain_id}/',         array($this, '_domainGetDetail'));
        $this->controllers->put   ('/domain/{domain_id}/',         array($this, '_domainUpdate'));
        $this->controllers->delete('/domain/{domain_id}/',         array($this, '_domainDelete'));
        $this->controllers->get   ('/domain/{domain_id}/account/', array($this, '_domainGetAccountCollection'));

        // Account actions
        $this->controllers->get   ('/account/',                    array($this, '_accountGetCollection'));
        $this->controllers->post  ('/account/',                    array($this, '_accountCreate'));
        $this->controllers->get   ('/account/{account_id}/',       array($this, '_accountGetDetail'));
        $this->controllers->delete('/account/{account_id}/',       array($this, '_accountDelete'));
        $this->controllers->put   ('/account/{account_id}/',       array($this, '_accountUpdate'));
        $this->controllers->get   ('/account/{account_id}/quota/', array($this, '_accountGetQuota'));

        // Class of service actions
        $this->controllers->get('/cos/',                 array($this, '_cosGetCollection'));
        $this->controllers->get('/cos/{cos_id}/',        array($this, '_cosGetDetail'));

        return $this->controllers;
    }

    /**
     * Returns the response to GET /domain/
     * which returns a collection of domains from the ZCS server
     *
     * @return \App\Rest\Response
     */
    public function _domainGetCollection()
    {
        $domains = $this->nugService->getDomainList();
        return new Rest\Response($domains);
    }

    /**
     * Returns the response to GET /domain/{domain_id}/
     * which returns the details of a domain identified by $domain_id
     * @param string $domain_id
     * @return \App\Rest\Response
     */
    public function _domainGetDetail($domain_id)
    {
        $domain = $this->nugService->getDomain($domain_id);
        return new Rest\Response($domain);
    }

    /**
     * Returns the response to POST /domain/{domain_id}/
     * which updates a domain identified by $domain_id
     * @param integer $domain_id
     * @return \App\Rest\Response
     */
    public function _domainUpdate($domain_id)
    {
        $domainData = $this->app['request']->payload;
        $domainEntity = \Zimbra\ZCS\Entity\Domain::createFromJson($domainData);
        $domainEntity->setId($domain_id);

        $updatedDomainEntity = $this->nugService->updateDomain($domainEntity);

        return new Rest\Response(
            array('domain' => $updatedDomainEntity), // Response body, encoded as JSON
            200, // Status code
            array('Location' => $this->app['config.domain'] . '/nug/domain/' . $updatedDomainEntity['id'] . '/') // Extra headers
        );
    }

    /**
     * Returns the response to PUT /domain/
     * which creates a new domain
     * @return \App\Rest\Response
     */
    public function _domainCreate()
    {
        $domainData = $this->app['request']->payload;
        $domainEntity = \Zimbra\ZCS\Entity\Domain::createFromJson($domainData);
        $newDomainEntity = $this->nugService->createDomain($domainEntity);

        return new Rest\Response(
            array('domain' => $newDomainEntity), // Response body, encoded as JSON
            201, // Status code
            array('Location' => $this->app['config.domain'] . '/nug/domain/' . $newDomainEntity['id'] . '/') // Extra headers
        );
    }

    /**
     * Returns the response to DELETE /domain/{domain_id}
     * which removes an existing domain
     * @param $domain_id
     * @return \App\Rest\Response
     */
    public function _domainDelete($domain_id)
    {
        $result = $this->nugService->deleteDomain($domain_id);

        return new Rest\Response(array(
            'success' => $result,
            'message' => 'The domain has been successfully deleted'
        ));
    }

    /**
     * Returns the response to GET /cos/
     * which returns a collection of available "Classes of Service" from the ZCS server
     *
     * @return \App\Rest\Response
     */
    public function _cosGetCollection()
    {
        $cosList = $this->nugService->getCosList();
        return new Rest\Response($cosList);
    }

    /**
     * Returns the response to GET /cos/{cos_id}/
     * which returns the details of a COS identified by $cos_id
     * @param integer $cos_id
     * @return \App\Rest\Response
     */
    public function _cosGetDetail($cos_id)
    {
        $cos = $this->nugService->getCos($cos_id);
        return new Rest\Response($cos);
    }

    /**
     * Returns a list of accounts from a given domain identified by $domain_id
     *
     * @param string $domain_id
     * @return \App\Rest\Response
     */
    public function _domainGetAccountCollection($domain_id)
    {
        $accounts = $this->nugService->getAccountListByDomain($domain_id);
        return new Rest\Response($accounts);
    }

    /**
     * Returns the response to GET /account/
     * which returns a collection of accounts from the ZCS server
     *
     * @return \App\Rest\Response
     */
    public function _accountGetCollection()
    {
        $domains = $this->nugService->getAccountList();
        return new Rest\Response($domains);
    }

    /**
     * Returns the response to GET /account/{account_id}/
     * which returns the details of an account identified by $account_id
     * @param string $account_id
     * @return \App\Rest\Response
     */
    public function _accountGetDetail($account_id)
    {
        $account = $this->nugService->getAccount($account_id);
        return new Rest\Response($account);
    }

    /**
     * Returns the response to PUT /account/
     * which creates a new account
     * @return \App\Rest\Response
     */
    public function _accountCreate()
    {
        $accountData = $this->app['request']->payload;
        $accountEntity = \Zimbra\ZCS\Entity\Account::createFromJson($accountData);
        $newAccountEntity = $this->nugService->createAccount($accountEntity);

        return new Rest\Response(
            array('account' => $newAccountEntity), // Response body, encoded as JSON
            201, // Status code
            array('Location' => $this->app['config.domain'] . '/nug/account/' . $newAccountEntity['id'] . '/') // Extra headers
        );
    }

    /**
     * Returns the response to POST /account/{id}/
     * which updates an account identified by $id
     * @param integer $account_id
     * @return \App\Rest\Response
     */
    public function _accountUpdate($account_id)
    {
        $accountData = $this->app['request']->payload;
        $accountEntity = \Zimbra\ZCS\Entity\Account::createFromJson($accountData);
        $accountEntity->setId($account_id);

        $updatedAccountEntity = $this->nugService->updateAccount($accountEntity);

        return new Rest\Response(
            array('account' => $updatedAccountEntity), // Response body, encoded as JSON
            200, // Status code
            array('Location' => $this->app['config.domain'] . $updatedAccountEntity['uri']) // Extra headers
        );
    }

    /**
     * Returns the response to DELETE /account/{account_id}
     * which removes an existing account
     * @param $account_id
     * @return \App\Rest\Response
     */
    public function _accountDelete($account_id)
    {
        $result = $this->nugService->deleteAccount($account_id);

        return new Rest\Response(array(
            'success' => $result,
            'message' => 'The account has been successfully deleted'
        ));
    }

    public function _accountGetQuota($account_id)
    {
        $result = $this->nugService->getAccountQuota($account_id);
        return new Rest\Response($result);
    }
}