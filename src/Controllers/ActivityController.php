<?php
/**
 * File Name : ActivityController.php
 * Path : 'C:\xampp\htdocs\Project\timesheet\src\controllers'
 * @author : Alokik Pathak
 * Created : 18/09/2018
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
class ActivityController{

    public $fileMaker;
    
    public function __construct($container)
    {
        // make the container available in the class
        $this->container = $container;
        $this->log = $container->get('logger');

        require_once("../services/configFileMaker.php");
        require_once('../src/config.php');

        $this->fileMaker = new FileMakerDB(FM_FILE, FM_HOST, FM_USER, FM_PASSWORD, $this->log);

    }

    /**
     * Fetch all Acitivity from database
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface $response
     */
    public function getAllActivity($request, $response){

        $layout = LAYOUT_ACTIVITY;
        $criteria = '___kp_Id';
        $criterion = '>0';

        $criteriaData = array(
            $criteria => $criterion
        );

        $serverResponse = $this->fileMaker->getActivity($layout, $criteriaData);

        return $response->withJson($serverResponse);
        
    }

    /**
     * Fetch all Acitivity from database of a particular userId
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface $response
     */
    public function getActivity($request, $response){

        $userId = $request->getAttribute('userId');

        $layout = LAYOUT_ACTIVITY;
        $criteria = '__kf_UserID';
        $criterion = $userId;

        $criteriaData = array(
            $criteria => $criterion
        );

        $serverResponse = $this->fileMaker->getActivity($layout, $criteriaData);

        return $response->withJson($serverResponse);
        
    }

    /**
     * Add a new Activity to FileMaker Server Database
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface $response
     */
    public function addActivity($request, $response){
        
        $data = array(
                    '__kf_UserID' => $request->getParam('__kf_UserID'),
                    'Name' => $request->getParam('Name')
                    );
        $layout = LAYOUT_ACTIVITY ;
        
        $serverResponse = $this->fileMaker->add($data, $layout);

        return $response->withJson($serverResponse);
    }

    /**
     * Delete a activity from database
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface $response
     */
    public function deleteActivity($request, $response){
        
        $activityId = $request->getAttribute('id');
        $layout = LAYOUT_ACTIVITY;
        $criteria = '___kp_Id';
        $criterion = $activityId;

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
     * Update a User to Database
     * 
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface $response
     */
    public function updateActivity($request, $response){
        
        $data = array(
                    '___kp_Id' => $request->getParam('___kp_Id'),
                    'Name' => $request->getParam('Name')
                );

        $layout = LAYOUT_ACTIVITY;
        $criteria = '___kp_Id';
        $criterion = $data['___kp_Id'];

        $recordId = $this->fileMaker->getRecordId($layout, $criteria, $criterion);
        
        if($recordId == -1){
            return $response->withJson(array('error'=>"Couldn't get the record Id", 'code'=>400),400);
        }
        
        $serverResponse = $this->fileMaker->update( $layout, $recordId, $data );
        
        return $response->withJson($serverResponse);
        
    }


    /**
     * Filters and fetch all Acitivities from database.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return \Psr\Http\Message\ResponseInterface $response
     */
    public function filterUserActivities($request, $response){

        $userId = $request->getAttribute('userId');
        $filterKey = $request->getAttribute('key');

        $layout = LAYOUT_ACTIVITY;
        $criteria = '__kf_UserID';
        $criterion = $userId;

        $criteriaData = array(
            '___kp_Id' => $filterKey,
            'Name' => "*".$filterKey."*",
            'CreatedBy' => "*".$filterKey."*",
            'ModifiedBy' => "*".$filterKey."*"
        );

        $serverResponse = $this->fileMaker->filterActivity($layout, $userId, $criteriaData);

        return $response->withJson($serverResponse);
        
    }


}