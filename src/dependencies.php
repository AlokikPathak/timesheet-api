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

// Injecting HomeController which handles routes methods
$container['HomeController'] = function( $c ){
    return new \Src\Controllers\HomeController($c);
};

// Injecting ActivityController which handles routes methods
$container['ActivityController'] = function( $c ){
    return new \Src\Controllers\ActivityController($c);
};

