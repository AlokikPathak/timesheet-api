<?php
/**
 * File Name : HomeController.php
 * Path : 'C:\xampp\htdocs\Project\timesheet\src\controllers'
 * @author : Alokik Pathak
 * Created : 10/08/2018
 */

 
namespace controllers;

use PsrHttpMessageServerRequestInterface as Request;
use PsrHttpMessageResponseInterface as Response;

class HomeController{

    public function __construct($container)
    {
        // make the container available in the class
        $this->container = $container;
    }


    public function users(Request $request, Response $response, $args)
    {
        echo"Inside users controllers";
        $data = array('Message'=>"Welcome to controllers");
        echo $data;
      //  return $response->withJson($data);
        return $response;
    }

    public function create(Request $request, Response $response, $args)
    {
        // proceed to creating a new user
    }


    public function delete(Request $request, Response $response, $args)
    {
        // proceed to deleting a new user
    }


    //...

}