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

$app->group('/api', function() use ($app) {

    // For users
    $app->group('/users', function() use ($app) {

        /**
         * Get All users from FileMaker Server Database
         * 
         */
        $app->get('', 'HomeController:getAllUsers');

        /**
         * Get a single user from FileMaker Server Database
         * 
         */
        $app->get('/{id}', 'HomeController:getUser');

        /**
         * Get All users from FileMaker Server Database
         * 
         */
        $app->get('/filter/{key}', 'HomeController:getAllUsersFiltered');

        /**
         * Add a new User to Database
         * 
         */
        $app->post('', 'HomeController:addUser');

        /**
         * Update a User to Database
         * 
         */
        $app->put('', 'HomeController:updateUser');

        /**
         * Deletes a user using its id
         *  
         */
        $app->delete('/{id}', 'HomeController:deleteUser');

        /** 
         * Authenticate Login credentials
         * 
         */
        $app->post('/login', 'HomeController:authenticateLoginCredentials');

    });

    // For Activity
    $app->group('/activity', function() use ($app) {

        /**
         * Fetching details of all activities
         * 
         */
        $app->get('','ActivityController:getAllActivity');

        /**
         * Fetching all activity of a particular UserId
         * 
         */
        $app->get('/{userId}', 'ActivityController:getActivity');

        /**
         * Add a new activity to Database 
         * 
         */
        $app->post('', 'ActivityController:addActivity');

        /**
         * Deletes a activity
         *  
         */
        $app->delete('/{id}', 'ActivityController:deleteActivity');

        /**
         * Update a Activity to Database
         * 
         */
        $app->put('', 'ActivityController:updateActivity');

        /**
         * Filter user activities 
         * 
         */
        $app->get('/{userId}/{key}', 'ActivityController:filterUserActivities');
            
    });

});

?>