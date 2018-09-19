<?php
/**
 * File Name : HomeController.php
 * Path : 'C:\xampp\htdocs\Project\timesheet\src\controllers'
 * @author : Alokik Pathak
 * Created : 10/08/2018
 */

namespace Src\Controllers;

use FileMakerDB;
use PsrHttpMessageServerRequestInterface as Request;
use PsrHttpMessageResponseInterface as Response;

/**
 * Handles routes requests and perform requested methods
 * 
 * @author : Alokik Pathak 
 */
class HomeController{

    public $fileMaker;

    public function __construct($container){

        // make the container available in the class
        $this->container = $container;
        $this->log = $container->get('logger');

        require_once("../services/configFileMaker.php");
        require_once('../src/config.php');

        $this->fileMaker = new FileMakerDB(FM_FILE, FM_HOST, FM_USER, FM_PASSWORD, $this->log);

    }


    /**
     * Fetch all user from database
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface $response
     */
    public function getAllUsers($request, $response){

        $layout = LAYOUT_USERS;
        $criteria = '___kp_UserID';
        $criterion = '>0';

        $criteriaData = array(
            $criteria => $criterion
        );

        $serverResponse = $this->fileMaker->get($layout, $criteriaData);
        
        return $response->withJson($serverResponse);
    }


    /**
     * Fetch all user from database based on Filter Key
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface $response
     */
    public function getAllUsersFiltered( $request, $response){

        $filterKey = $request->getAttribute('key');
        $layout = LAYOUT_USERS;
        
        $criteriaData = array(
            '___kp_UserID' => $filterKey,
            'FirstName' => "*".$filterKey."*",
            'LastName' => "*".$filterKey."*",
            '_ka_Email' => "=="."*".$filterKey."*",
            '_ka_Mobile' => "*".$filterKey."*",
            'Designation' => "*".$filterKey."*",
            'Address' => "*".$filterKey."*"
        );

        $serverResponse = $this->fileMaker->getFiltered($layout, $criteriaData);
        
        return $response->withJson($serverResponse);
    }


    /**
     * Fetch a single user from database
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface $response
     */
    public function getUser( $request, $response){
        
        $userId = $request->getAttribute('id');
        $layout = LAYOUT_USERS;
        $criteria = '___kp_UserID';
        $criterion = $userId;

        $criteriaData = array(
            $criteria => $criterion
        );

        $serverResponse = $this->fileMaker->get($layout, $criteriaData);	
        
        return $response->withJson($serverResponse);
    }


    /**
     * Add a new user to FileMaker Server Database
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface $response
     */
    public function addUser($request, $response){
        
        $userData = array(
                    'FirstName' => $request->getParam('FirstName'),
                    'LastName' => $request->getParam('LastName'),
                    '_ka_Email'	=> $request->getParam('_ka_Email'),
                    '_ka_Mobile' => $request->getParam('_ka_Mobile'),
                    'Designation' => $request->getParam('Designation'),
                    'Address' => $request->getParam('Address'),
                    'Password' => strtoupper( md5($request->getParam('Password')) ),
                
                    );
        $layout = LAYOUT_USERS;
                    
        $serverResponse = $this->fileMaker->add($userData, $layout);

        return $response->withJson($serverResponse);
    }

    
    /**
     * Update a User to Database
     * 
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface $response
     */
    public function updateUser( $request, $response ){
        
        $userData = array(
                    '___kp_UserID' => $request->getParam('userId'),
                    'FirstName' => $request->getParam('firstName'),
                    'LastName' => $request->getParam('lastName'),
                    '_ka_Email'	=> $request->getParam('email'),
                    '_ka_Mobile' => $request->getParam('mobile'),
                    'Designation' => $request->getParam('designation'),
                    'Address' => $request->getParam('address')
                    );

        $layout = LAYOUT_USERS;
        $criteria = '___kp_UserID';
        $criterion = $userData['___kp_UserID'];

        $recordId = $this->fileMaker->getRecordId($layout, $criteria, $criterion);
        
        if($recordId == -1){
            return $response->withJson(array('error'=>"Couldn't get the record Id", 'code'=>400),400);
        }
        
        $serverResponse = $this->fileMaker->update( $layout, $recordId, $userData );
        
        return $response->withJson($serverResponse);
        
    }


    /**
     * Delete a user from database
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface $response
     */
    public function deleteUser( $request, $response ){
        
        $userId = $request->getAttribute('id');
        $layout = LAYOUT_USERS;
        $criteria = '___kp_UserID';
        $criterion = $userId;

        $recordId = $this->fileMaker->getRecordId($layout, $criteria, $criterion);

        if($recordId == -1){
            return $response->withJson(array('error'=>"Couldn't get the record Id", 'code'=>400),400);
        }
        
        $status = $this->fileMaker->delete($layout, $recordId);

        if( $status['code'] == 401){
            return $response->withJson($status, 401);
        }
        
        return $response->withJson($status, 200);
    }


    /** 
     * Authenticate Login credentials
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface $response
     */
    public function authenticateLoginCredentials( $request, $response){

        $email = $request->getParam('email');
        $password = $request->getParam('password');
        $password = md5($password);

        $password = strtoupper($password);
        $layout = LAYOUT_USERS;

        $loginCredentials = array(
                        '_ka_Email'=> $email,
                        'Password' => $password
                        );                

        $serverResponse = $this->fileMaker->authenticateLoginCredentials($loginCredentials, $layout);

        return  $response->withJson($serverResponse);

    }


}