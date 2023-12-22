<?php
use \Core\View;
use \App\Library\RequirePage;


session_start();
$_SESSION['url'] = $_SERVER['REQUEST_URI']; 



/**
 * Front controller
 *
 * PHP version 7.0
 */

/**
 * Composer
 */
require dirname(__DIR__) . '/vendor/autoload.php';


/**
 * Error and Exception handling
 */
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');


/**
 * Routing
 */
$router = new Core\Router();

// Add the routes
$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('{controller}/{action}');
$router->add('{controller}/{action}/{id:\d+}');

// echo "<pre>";
// print_r($router);
// print_r($_SERVER['QUERY_STRING']);
$router->dispatch($_SERVER['QUERY_STRING']);

