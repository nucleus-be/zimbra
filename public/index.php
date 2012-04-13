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
$app['config.domain'] = "http://api.chris.dev.nucleus.be";

// Register the App namespace to the autoloader
$app['autoloader']->registerNamespace('App', APP_ROOT.'/lib');
$app['autoloader']->registerNamespace('Zimbra', APP_ROOT.'/lib/Nucleus/src/');

// Register the validation service
$app->register(new Silex\Provider\ValidatorServiceProvider(), array(
    'validator.class_path'    => APP_ROOT.'/lib/'
));

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
            $data = array(
                'error' => true,
                'message' => $response->getErrorMessage()
            );
            if($response->getErrors()){
                $data['errors'] = $response->getErrors();
            }
            $response->setContent($writer->setData($data)->output());
        } else {
            $response->setContent($writer->setData($response->getData())->output());
        }
    }
});

// Register classes in Pimple DI
$app['service_nug'] = function() use ($app){
    return new \App\Service\Nug($app);
};
$app['zimbra_admin_domain'] = function() use ($app){
    $zimbraDomainAdmin = new \Zimbra\ZCS\Admin\Domain('mail.webruimte.eu', 7071);
    $zimbraDomainAdmin->auth('admin', 'kl!tr34h');
    return $zimbraDomainAdmin;
};
$app['zimbra_admin_cos'] = function() use ($app){
    $zimbraCosAdmin = new \Zimbra\ZCS\Admin\Cos('mail.webruimte.eu', 7071);
    $zimbraCosAdmin->auth('admin', 'kl!tr34h');
    return $zimbraCosAdmin;
};

// Mount controllers
$app->mount('/nug', new App\Controller\Nug());

// Run the application
$app->run();