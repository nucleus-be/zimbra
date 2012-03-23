<?php

namespace App\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use App\Rest;

class Nug implements ControllerProviderInterface
{
    protected $app;
    protected $controllers;

    public function init($app)
    {
        $this->app = $app;
        $this->controllers = new ControllerCollection();
    }

    public function connect(Application $app)
    {
        $this->init($app);

        // List of domains
        $this->controllers->get('/domain/', function() use ($app){
            return "Here is a list with all domains ...";
        });

        // Get domain info
        $this->controllers->get('/domain/{domain_id}/', function($domain_id) use ($app){
            if($domain_id < 10) {
                throw new Rest\Exception\ResourceNotFound("The domain resource with id {$domain_id} does not exist");
            } elseif($domain_id < 20) {
                throw new Rest\Exception\AccessDenied();
            } else {
                return new Rest\Response("Here is some general information about domain with id " . $domain_id);
            }
        });

        // Update a domain
        $this->controllers->post('/domain/{domain_id}/', function($domain_id) use ($app){
            return "Update domain with id " . $domain_id;
        });

        // Create a domain
        $this->controllers->post('/domain/', function() use ($app){
            return "Create a new domain";
        });

        $this->controllers->get('/domain/{domain_id}/account/', function($domain_id) use ($app){
            return "Here is a list with accounts in domain ".$domain_id;
        });

        $this->controllers->get('/domain/{domain_id}/account/{account_id}/', function($domain_id, $account_id) use ($app){
            return "Here is info about account {$account_id} from domain ".$domain_id;
        });

        return $this->controllers;
    }
}