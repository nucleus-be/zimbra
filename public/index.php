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

// Register the App namespace to the autoloader
$app['autoloader']->registerNamespace('App', APP_ROOT.'/lib');

// Handle errors
$app->error(function (\Exception $e, $code) {
    return ExceptionProcessor::process($e);
});

// After filter to create the response based on the requested format (default = json)
$app->after(function (Request $request, Response $response) {
    if($response instanceof App\Rest\Response){
        $format = $request->query->get('format', 'json');
        $writer = App\Rest\Response\Writer::factory($format);
        $response->headers->add($writer->getHeaders());
        if ($response->isError()){
            $response->setContent($writer->setData(array(
                'error' => true,
                'message' => $response->getErrorMessage()
            ))->output());
        } else {
            $response->setContent($writer->setData($response->getData())->output());
        }
    }
});

// Mount controllers
$app->mount('/nug', new App\Controller\Nug());

// Run the application
$app->run();