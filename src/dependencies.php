<?php
// DIC configuration

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

// Override the default Not Allowed Error Handler
$container['notAllowedHandler'] = function ($c) {
    return function (Request $request, Response $response, $methods) use ($c) {
        return $c['response']
            ->withJson(
                array("Error"=>"Method not allowed.",
                "Allow"=>'Method must be one of: '.implode(',', $methods)),
                405
            );
    };
};

// Override the default Not Found Error Handler
$container['notFoundHandler'] = function ($c) {
    return function (Request $request, Response $response) use ($c) {
        return $c['response']
            ->withJson(
                array("Error"=>"Page not found."),
                404
            );
    };
};

// Override the default Php Error Handler
$c['phpErrorHandler'] = function ($c) {
    return function (Request $request, Response $response, $error) use ($c) {
        return $c['response']
            ->withJson(
                array("Error"=>"Something went wrong."),
                500
            );
    };
};

// Injecting HomeController which handles routes methods
$container['HomeController'] = function( $c ){
    return new \Src\Controllers\HomeController($c);
};

// Injecting ActivityController which handles routes methods
$container['ActivityController'] = function( $c ){
    return new \Src\Controllers\ActivityController($c);
};

