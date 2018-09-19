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

define('PATH_A', '/api/users' );
define("PATH_B", '/api/activity');

/**
 * Get All users from FileMaker Server Database
 * 
 */
$app->get( PATH_A, 'HomeController:getAllUsers');

/**
 * Get All users from FileMaker Server Database
 * 
 */
$app->get( PATH_A.'/filter/{key}', 'HomeController:getAllUsersFiltered');

/**
 * Get a single user from FileMaker Server Database
 * 
 */
$app->get( PATH_A.'/{id}', 'HomeController:getUser');

/**
 * Add a new User to Database
 * 
 */
$app->post( PATH_A, 'HomeController:addUser');

/**
 * Update a User to Database
 * 
 */
$app->put( PATH_A, 'HomeController:updateUser');

/**
 * Deletes a user using its id
 *  
 */
$app->delete( PATH_A.'/{id}', 'HomeController:deleteUser');

/** 
 * Authenticate Login credentials
 * 
 */
$app->post( PATH_A.'/login', 'HomeController:authenticateLoginCredentials');

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
$app->post(PATH_B, 'ActivityController:addActivity');

/**
 * Deletes a activity
 *  
 */
$app->delete(PATH_B.'/{id}', 'ActivityController:deleteActivity');

/**
 * Update a Activity to Database
 * 
 */
$app->put(PATH_B, 'ActivityController:updateActivity');

/**
 * Filter user activities 
 * 
 */
$app->get(PATH_B.'/{userId}/{key}', 'ActivityController:filterUserActivities');


?>