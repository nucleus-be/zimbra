<?php
namespace App;

use Silex\Application;
use Silex\ControllerCollection;

abstract class Controller
{
    /**
     * @var \Silex\Application
     */
    protected $app;

    /**
     * @var \Silex\ControllerCollection
     */
    protected $controllers;

    /**
     * Initializes the controller by registering the Silex Application and
     * creating a controller collection
     *
     * @param \Silex\Application $app
     */
    public function init(Application $app)
    {
        $this->app = $app;
        $this->controllers = new ControllerCollection();
    }
}