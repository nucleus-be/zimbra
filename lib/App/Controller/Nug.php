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
     * Returns routes to connect to the given application.
     *
     * @param \Silex\Application $app
     * @return \Silex\ControllerCollection
     */
    public function connect(Application $app)
    {
        $this->init($app);

        $this->controllers->get ('/domain/',             array($this, '_domainGetCollection'));
        $this->controllers->put ('/domain/',             array($this, '_domainCreate'));
        $this->controllers->get ('/domain/{domain_id}/', array($this, '_domainGetDetail'));
        $this->controllers->post('/domain/{domain_id}/', array($this, '_domainUpdate'));

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
        return new Rest\Response(array(
            'status' => "Here is a list with all domains ..."
        ));
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
        if($domain_id < 10) {
            throw new Rest\Exception\ResourceNotFound("The domain resource with id {$domain_id} does not exist");
        } elseif($domain_id < 20) {
            throw new Rest\Exception\AccessDenied();
        } else {
            return new Rest\Response(array(
                'domain_id' => $domain_id,
                'name'      => 'Test domain'
            ));
        }
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
}