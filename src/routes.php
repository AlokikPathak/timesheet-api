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

require '..\controllers\HomeController.php';
require_once 'config.php';

define('PATH', '/api/users' );

$app = new \Slim\App;

$app->get('/api', 'controllers\HomeController:users');

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
	
	require_once("../services/configFileMaker.php");
	$layout = LAYOUT;
	$criteria = 'UserID';
	$criterion = '>0';
	$fileMaker = new FileMakerDB(FM_FILE, FM_HOST, FM_USER, FM_PASSWORD);
	$serverResponse = $fileMaker->get( $layout, $criteria, $criterion );
	
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
	$layout = LAYOUT;
	$criteria = 'UserID';
	$criterion = $userId;
	
	require_once("../services/configFileMaker.php");
	
	$fileMaker = new FileMakerDB(FM_FILE, FM_HOST, FM_USER, FM_PASSWORD);
	$serverResponse = $fileMaker->get( $layout, $criteria, $criterion );
	
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
	$layout = LAYOUT;
	
	require_once("../services/configFileMaker.php");
	$fileMaker = new FileMakerDB(FM_FILE, FM_HOST, FM_USER, FM_PASSWORD);
	
	$serverResponse = $fileMaker->add($userData, $layout);
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

	$layout = LAYOUT;
	$criteria = 'UserID';
	$criterion = $userData['UserID'];

	require_once("../services/configFileMaker.php");
	$fileMaker = new FileMakerDB(FM_FILE, FM_HOST, FM_USER, FM_PASSWORD);
	
	$recordId = $fileMaker->getRecordId($layout, $criteria, $criterion);
	
	if($recordId == -1){
		echo json_encode(array('error'=>"Couldn't get the record Id", 'code'=>400));
		return $response->withJson(array('error'=>"Couldn't get the record Id"),400);
	}
	
	$serverResponse = $fileMaker->update( $layout, $recordId, $userData );
					
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
	$layout = LAYOUT;
	$criteria = 'UserID';
	$criterion = $userId;

	require_once("../services/configFileMaker.php");
	
	$fileMaker = new FileMakerDB(FM_FILE, FM_HOST, FM_USER, FM_PASSWORD);

	$recordId = $fileMaker->getRecordId($layout, $criteria, $criterion);
	
	if($recordId == -1){
		echo json_encode(array('error'=>"Couldn't get the record Id", 'code'=>400));
		return $response->withJson(array('error'=>"Couldn't get the record Id"),400);
	}
	
	$status = $fileMaker->delete($layout, $recordId);
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
	$layout = LAYOUT;
	
	require_once("../services/configFileMaker.php");
	
	$fileMaker = new FileMakerDB(FM_FILE, FM_HOST, FM_USER, FM_PASSWORD);
	$serverResponse = $fileMaker->authenticateLoginCredentials($loginCredentials, $layout);

	echo json_encode($serverResponse);
	return  $response->withJson($serverResponse);

}


?>