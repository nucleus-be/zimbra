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
        $this->controllers->get ('/domain/',             array($this, '_domainGetCollection'));
        $this->controllers->put ('/domain/',             array($this, '_domainCreate'));
        $this->controllers->get ('/domain/{domain_id}/', array($this, '_domainGetDetail'));
        $this->controllers->post('/domain/{domain_id}/', array($this, '_domainUpdate'));

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
     * @param integer $domain_id
     * @return \App\Rest\Response
     * @throws \App\Rest\Exception\AccessDenied|\App\Rest\Exception\ResourceNotFound
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
        return new Rest\Response(array(
            'status' => "Update domain with id " . $domain_id
        ));
    }

    /**
     * Returns the response to PUT /domain/
     * which creates a new domain
     * @return \App\Rest\Response
     */
    public function _domainCreate()
    {
        return new Rest\Response(array(
            'status' => "Create new domain"
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
     * @throws \App\Rest\Exception\AccessDenied|\App\Rest\Exception\ResourceNotFound
     */
    public function _cosGetDetail($cos_id)
    {
        $cos = $this->nugService->getCos($cos_id);
        return new Rest\Response($cos);
    }
}