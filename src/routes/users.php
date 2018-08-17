<?php
/**
 * File Name : users.php
 * Location : C:\xampp\htdocs\Project\timesheet\src\routes
 * Created On : 08/08/2018
 *
 * @author : Alokik Pathak
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use controllers\HomeController;

define('PATH', '/api/users' );

$app = new \Slim\App;

$app->get('/api/hello', HomeController::class.':users');

/**
 * Get All users from FileMaker Server Database
 */
$app->get( PATH, function(Request $request, Response $response){
	
	getAllUsers($request, $response);
});


/**
 * Get a single user from FileMaker Server Database
 */
$app->get( PATH.'/{id}', function (Request $request, Response $response){
	
	getUser($request, $response);

});


/**
 * Add a new User to Database
 * 
 */
$app->post( PATH, function(Request $request, Response $response){
	
	addUser($request, $response);

});


/**
 * Update a User to Database
 * 
 */
$app->put( PATH, function(Request $request, Response $response){
	
	updateUser($request, $response);

});


/**
 * Deletes a user 
 */
$app->delete( PATH.'/{id}', function(Request $request, Response $response){
	
	deleteUser($request, $response);
});


/** 
 * Authenticate Login credentials
 */
$app->post( PATH.'/login', function(Request $request, Response $response){
	
	authenticateLoginCredentials($request, $response);
		
});


/**
 * Fetch all user from database
 *
 * @param \Psr\Http\Message\ServerRequestInterface $request
 * @param \Psr\Http\Message\ResponseInterface $response
 * @return \Psr\Http\Message\ResponseInterface $response
 */
function getAllUsers( $request, $response){
	
	require_once("configFileMaker.php");
	
	$fileMaker = new FileMakerDB();
	$serverResponse = $fileMaker->getAllUsers();
	
	echo json_encode($serverResponse);
	return $response->withJson($serverResponse);
}


/**
 * Fetch a single user from database
 *
 * @param \Psr\Http\Message\ServerRequestInterface $request
 * @param \Psr\Http\Message\ResponseInterface $response
 * @return \Psr\Http\Message\ResponseInterface $response
 */
function getUser($request, $response){
	
	$userId = $request->getAttribute('id');
	
	require_once("configFileMaker.php");
	
	$fileMaker = new FileMakerDB();
	$serverResponse = $fileMaker->getUser($userId);
	
	echo json_encode($serverResponse);
	return $response->withJson($serverResponse);
}


/**
 * Add a new user to FileMaker Server Database
 *
 * @param \Psr\Http\Message\ServerRequestInterface $request
 * @param \Psr\Http\Message\ResponseInterface $response
 * @return \Psr\Http\Message\ResponseInterface $response
 */
function addUser($request, $response){
	
	$userData = array(
				'FirstName' => $request->getParam('firstName'),
				'LastName' => $request->getParam('lastName'),
				'Email'	=> $request->getParam('email'),
				'Mobile' => $request->getParam('mobile'),
				'Department' => $request->getParam('department'),
				'Address' => $request->getParam('address'),
				'Password' => $request->getParam('password')
				);
				
	
	require_once("configFileMaker.php");
	$fileMaker = new FileMakerDB();
	
	$serverResponse = $fileMaker->addUser($userData);
	echo json_encode($serverResponse);
	return $response->withJson($serverResponse);
}


/**
 * Update a User to Database
 * 
 * @param \Psr\Http\Message\ServerRequestInterface $request
 * @param \Psr\Http\Message\ResponseInterface $response
 * @return \Psr\Http\Message\ResponseInterface $response
 */
function updateUser($request, $response){
	
	$userData = array(
				'UserID' => $request->getParam('userId'),
				'FirstName' => $request->getParam('firstName'),
				'LastName' => $request->getParam('lastName'),
				'Email'	=> $request->getParam('email'),
				'Mobile' => $request->getParam('mobile'),
				'Department' => $request->getParam('department'),
				'Address' => $request->getParam('address')
				);
				
	require_once("configFileMaker.php");
	$fileMaker = new FileMakerDB();
	$serverResponse = $fileMaker->updateUser($userData);
					
	echo json_encode($serverResponse);
	return $response->withJson($serverResponse);
	
}


/**
 * Delete a user from database
 *
 * @param \Psr\Http\Message\ServerRequestInterface $request
 * @param \Psr\Http\Message\ResponseInterface $response
 * @return \Psr\Http\Message\ResponseInterface $response
 */
function deleteUser($request, $response){
	
	$userId = $request->getAttribute('id');
	
	require_once("configFileMaker.php");
	
	$fileMaker = new FileMakerDB();
	$status = $fileMaker->deleteUser($userId);
	
	echo json_encode($status);
	
	if( $status['code'] == 401){
		return $response->withJson($status, 401);
	}
	
	return $response->withJson($status, 201);
}


/** 
 * Authenticate Login credentials
 *
 * @param \Psr\Http\Message\ServerRequestInterface $request
 * @param \Psr\Http\Message\ResponseInterface $response
 * @return \Psr\Http\Message\ResponseInterface $response
 */
function authenticateLoginCredentials($request, $response){
	
	$loginCredentials = array(
					'Email'=> $request->getParam('email'),
					'Password' => $request->getParam('password')
					);
	
	require_once("configFileMaker.php");
	
	$fileMaker = new FileMakerDB();
	$serverResponse = $fileMaker->authenticateLoginCredentials($loginCredentials);

	echo json_encode($serverResponse);
	return  $response->withJson($serverResponse);
}


?>