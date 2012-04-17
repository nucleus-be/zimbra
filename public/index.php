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
$app['autoloader']->registerNamespace('App',    APP_ROOT.'/lib');
$app['autoloader']->registerNamespace('Zimbra', APP_ROOT.'/lib/Nucleus/src/');
$app['autoloader']->registerNamespace('Util',   APP_ROOT.'/lib/Nucleus/src/');

// Register the validation service
$app->register(new Silex\Provider\ValidatorServiceProvider(), array(
    'validator.class_path'    => APP_ROOT.'/lib/'
));

// Handle errors
$app->error(function (\Exception $e, $code) {

    // Silex puts the errorcode $code in this callback, in case
    // a standard non-500 HTTP error is set it means the error is caused
    // by a missing route or something like that, else Silex returns a
    // 500 error in which case custom exceptions could be thrown and
    // we handle it with our Rest Response class
    if($code == 500) {
        return \App\Rest\Response::fromException($e, $code);
    }
});

// Validate data in the Request
$app->before(function(Request $request){
    // Validate JSON syntax/data
    if(stristr($request->getContentType(), 'json')){
        $data = \Util\JSON::decode($request->getContent());
        $request->payload = $data;
    }
});

// After filter to create the response based on the requested format (default = json)
$app->after(function (Request $request, Response $response) {
    if($response instanceof \App\Rest\Response){
        $writer = \App\Rest\Response\Writer::factory('json');
        $response->headers->add($writer->getHeaders());
        $response->setContent($writer->setData($response->getData())->output());
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
$app['zimbra_admin_account'] = function() use ($app){
    $zimbraAccountAdmin = new \Zimbra\ZCS\Admin\Account('mail.webruimte.eu', 7071);
    $zimbraAccountAdmin->auth('admin', 'kl!tr34h');
    return $zimbraAccountAdmin;
};

// Mount controllers
$app->mount('/nug', new App\Controller\Nug());

// Run the application
$app->run();