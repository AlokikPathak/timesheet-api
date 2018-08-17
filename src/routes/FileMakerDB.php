<?php
/**
 * File Name : FileMakerDB.php
 * Establish connection and perform database operations with FileMaker Server
 *  
 * @author : Alokik Pathak
 */

 
/**
 * Establish connection with FileMaker server
 * 
 * @author : Alokik Pathak 
 */
class FileMakerDB{

	private $FM_HOST = '172.16.9.42';
	private $FM_FILE = 'serviceApp.fmp12';
	private $FM_USER = 'Admin';
	private $FM_PASSWORD = '';
	public $fileMaker;
	public $error='';
    
	/**
	 * Class contructor establish connection with FileMaker Server
	 *
	 */
	function __construct( $fmFile, $fmHost, $fmUser, $fmPassword){
		require_once("FileMaker.php");
		
		/** Establish connection **/
		$this->fileMaker = new FileMaker( $fmFile, $fmHost, $fmUser, $fmPassword);
		$dbs = $fileMaker->listDatabases();
	
		/** Checking connection to server works without user credits */
		if(FileMaker::isError($dbs)) { 
			$this->error = $dbs->getMessage().' , '.$dbs.getCode(); 
		}
		
	}
	
	/**
	 * Connects with FileMaker Server
	 *
	 * @return boolean/FileMaker Object false if connection not established
     *     else returns FileMaker object connected to server
	 */
	public function connect(){
		
		/** Establish connection **/
		$fm = new FileMaker($this->FM_FILE, $this->FM_HOST, $this->FM_USER, $this->FM_PASSWORD);
		$dbs = $fm->listDatabases();
	
		/** Checking connection to server works without user credits */
		if(FileMaker::isError($dbs)) { 
			echo array('code'=>$dbs->getCode(), 'error'=>$dbs->getMessage());
			return false;
		}
		
		return $fm;
		
	}
	
	
	/**
	 * Deletes a User from FileMaker Database
	 *
	 * @param integer $userId contains UserID of user
	 * @return array() contains server response with details
	 */
	public function deleteUser($data){
	
		
		$findFieldCommand =& $this->fileMaker->newFindCommand($data['Layout']);
		$findFieldCommand->addFindCriterion("UserID", $data['id']);
		$result = $findFieldCommand->execute();
			
		//Check for Error
		if(FileMaker::isError($result)){
			return array('error'=>$result->getMessage(), 'code'=> $result.getCode());
		}
			
		//Storing the matching record
		$records = $result->getRecords();
		$record = $records[0];
		$recordId = $record->getRecordId();
			
		$deleteRecord =& $this->fileMaker->newDeleteCommand( $data['Layout'],$recordId);
		$result = $deleteRecord->execute();
			
		if(FileMaker::isError($result)){
			return array('error'=>$result->getMessage(), 'code'=> $result.getCode());
		}
		
		return array('status'=>'Succesfully deleted', 'code'=>200);
	}
	
	
	/**
	 * Add a new user to FileMaker database
	 *
	 * @param array() $userData contains user data 
	 * @return array() server response of the operation
	 */
	public function addUser( $userData){
		
		$findFieldCommand =& $this->fileMaker->newFindCommand( $userData['Layout'] );
	
		//Inserting Record to layout
		$insertRecord =& $this->fileMaker->newAddCommand( $userData['Layout'], $userData);
		$result = $insertRecord->execute();
		
		//checking for error
		if (FileMaker::isError($result)) {
			return array( 'code'=> $result.getCode(), 'error' =>$result->getMessage() );
		}
	
		return array('status'=>'Succesfully added a User!', 'code'=>201 );
	}
	
	
	/**
	 * Fetch a User information using the UserID from Database
	 *
	 * @param integer $userId contains UserID of the user
	 * @return array() server response
	 */
	public function getUser($userId){
		
		/** Connecting with FileMaker server **/
		$fm = $this->connect();
		
		$findFieldCommand =& $fm->newFindCommand("Users");
		//Specifying field and value to match
		$findFieldCommand->addFindCriterion("UserID", $userId);
		
		$result = $findFieldCommand->execute();
			
		if(FileMaker::isError($result)){
			return array( 'code'=> $result.getCode() ,'error' =>$result->getMessage());
		}
			
		//Storing the matching record
		$records = $result->getRecords();
		
		foreach( $records as $record ){
			$singleRecord = array(
								
								'UserID' => $record->getField('UserID'),
								'FirstName' => $record->getField('FirstName'),
								'LastName' => $record->getField('LastName'),
								'Email' => $record->getField('Email'),
								'Mobile' => $record->getField('Mobile'),
								'Department' => $record->getField('Department'),
								'Address' => $record->getField('Address')
					
							);
		}
		
		return $singleRecord;
	}
	
	
	/**
	 * Fetch all User information using the UserID from Database
	 *
	 * @return array() server response 
	 */
	public function getAllUsers(){
		
		/** Connecting with FileMaker server **/
		$fm = $this->connect();
		//using findCriterion command for searching in specific layout
		$findFieldCommand =& $fm->newFindCommand("Users");
		$findFieldCommand->addFindCriterion("UserID",">0");

		$result = $findFieldCommand->execute();
			
		//Check for Error
		if(FileMaker::isError($result)){
			return array('code'=> $result.getCode(), 'error'=> $result.getMessage());
		}

		//Storing the matching record
		$records = $result->getRecords();
		
		//number of records found
		$totalRecords = count($records);
		
		$allRecords = array();

		foreach ($records as $record) { 
		
			$allRecords[  ] =
							
							$singleRecord = array(
								
								'UserID' => $record->getField('UserID'),
								'FirstName' => $record->getField('FirstName'),
								'LastName' => $record->getField('LastName'),
								'Email' => $record->getField('Email'),
								'Mobile' => $record->getField('Mobile'),
								'Department' => $record->getField('Department'),
								'Address' => $record->getField('Address')
							
							);
		
		} 

		return $allRecords;
	}
	
	
	/** 
	 * Authenticate Login credentials
     *
     * @param array() $loginCredentials contains login credentials
	 * @return array() 
     */
	public function authenticateLoginCredentials($loginCredentials){
		
		$email = $loginCredentials['Email'];
		$password = $loginCredentials['Password'];
		
		/** Connecting with FileMaker server **/
		$fm = $this->connect();
		//using findCriterion command for searching in specific layout
		$findFieldCommand =& $fm->newFindCommand("Users");
		
		$findFieldCommand->addFindCriterion("Email",'=="'.$email . '"');
		
		$result = $findFieldCommand->execute();

		if(FileMaker::isError($result)){
			return array('code'=>$result.getCode(), 'error'=>$result->getMessage());
		}
			
		//Storing the matching record
		$records = $result->getRecords();
		$record = $records[0];
		$pswrd = $record->getField("Password");

		if(strcmp($password,$pswrd)!=0){
			return array('code'=>401, 'error'=>'Password is incorrect');
		}
		
		return array('code'=>200,'status'=>'Logged In Successfully');
		
	}
	
	
	/** 
	 * Update user data
     *
     * @param array() $userData contains user data
	 * @return array() server response
     */
	public function updateUser($userData, $layout){
		
		$userId = $userData['UserID'];
		
		$findFieldCommand =& $this->fileMaker->newFindCommand( $layout );
		
		$findFieldCommand->addFindCriterion("UserID", $userId);
		
		$result = $findFieldCommand->execute();
			
		if(FileMaker::isError($result)){
			return array('error'=>$result->getMessage(), 'code'=> $result.getCode());
		}
			
		//Storing the matching record
		$records = $result->getRecords();
		$record = $records[0];
		$recordId = $record->getRecordId();
		
		$updateRecord =& $this->fileMaker->newEditCommand($layout, $recordId, $userData);
		$result = $updateRecord->execute();
			
		//checking error
		if (FileMaker::isError($result)){
			return array(  'code'=> $result.getCode(), 'error'=>$result->getMessage() );
		}
			
		return array('status'=>'Succesfully Updated', 'code'=>200 );
	}
	
	
}
  
?>