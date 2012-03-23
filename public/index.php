<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Silex\Application;
use App\Rest\ExceptionProcessor;

// Define some constants
define('APP_ROOT', realpath(__DIR__.'/..'));
require_once APP_ROOT.'/lib/silex.phar';

// Create the Silex Application
$app = new Application();
$app['debug'] = true;
$app['autoloader']->registerNamespace('App', APP_ROOT.'/lib');

// Handle exceptions
$app->error(function (\Exception $e, $code) {
    return ExceptionProcessor::process($e);
});

// Mount controllers
$app->mount('/nug', new App\Controller\Nug());

// Filters
$app->after(function (Request $request, Response $response) {

    if($response instanceof App\Rest\Response){
        if ($response->isError()){
            $response->setContent($response->getErrorMessage());
        } else {
            $writer = new App\Rest\Response\Writer\Json();
            $response->setContent($writer->setData($response->getData())->output());
            $response->headers->add($writer->getHeaders());
        }
    }

});

// Run the application
$app->run();