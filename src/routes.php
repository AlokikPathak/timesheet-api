<?php
/**
 * File Name : routes.php
 * Location : C:\xampp\htdocs\Project\timesheet\src\routes
 * Created On : 08/08/2018
 *
 * @author : Alokik Pathak
 */


use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once 'config.php';

define('PATH', '/api/users' );
define("PATH_B", '/api/activity');

/**
 * Get All users from FileMaker Server Database
 * 
 */
$app->get( PATH, 'HomeController:getAllUsers');

/**
 * Get All users from FileMaker Server Database
 * 
 */
$app->get( PATH.'/filter/{key}', 'HomeController:getAllUsersFiltered');

/**
 * Get a single user from FileMaker Server Database
 * 
 */
$app->get( PATH.'/{id}', 'HomeController:getUser');

/**
 * Add a new User to Database
 * 
 */
$app->post( PATH, 'HomeController:addUser');

/**
 * Update a User to Database
 * 
 */
$app->put( PATH, 'HomeController:updateUser');

/**
 * Deletes a user using its id
 *  
 */
$app->delete( PATH.'/{id}', 'HomeController:deleteUser');

/** 
 * Authenticate Login credentials
 * 
 */
$app->post( PATH.'/login', 'HomeController:authenticateLoginCredentials');

/**
 * Fetching details of all activities
 * 
 */
$app->get(PATH_B,'ActivityController:getAllActivity');

/**
 * Fetching all activity of a particular UserId
 * 
 */
$app->get(PATH_B.'/{userId}', 'ActivityController:getActivity');

/**
 * Add a new activity to Database 
 * 
 */
$app->post( PATH_B, 'ActivityController:addActivity');

/**
 * Deletes a activity
 *  
 */
$app->delete( PATH_B.'/{id}', 'ActivityController:deleteActivity');

/**
 * Update a Activity to Database
 * 
 */
$app->put( PATH_B, 'ActivityController:updateActivity');

/**
 * Filter user activities 
 * 
 */
$app->get(PATH_B.'/{userId}/{key}', 'ActivityController:filterUserActivities');


?>