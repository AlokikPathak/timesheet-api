<?php
/**
 * File Name : configFileMaker.php
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

	public $fileMaker;
	public $error ='';
    
	/**
	 * Contructor establish connection with FileMaker Server
	 *
	 */
	function __construct( $fmFile, $fmHost, $fmUser, $fmPassword){
		require_once("FileMaker.php");
		
		/** Establish connection **/
		$this->fileMaker = new FileMaker( $fmFile, $fmHost, $fmUser, $fmPassword);
		$dbs = $this->fileMaker->listDatabases();
	
		/** Checking connection to server works without user credits */
		if(FileMaker::isError($dbs)) { 
			$this->error = $dbs->getMessage().' , '.$dbs.getCode(); 
		}
		
	}
	
	/**
	 * Deletes a User from FileMaker Database
	 *
	 * @param string $layout contains the name of layout
	 * @param integer $recordId contains recordId of particular record
	 * @return array() contains server response with details
	 */
	public function delete( $layout, $recordId ){
		
		$deleteRecord =& $this->fileMaker->newDeleteCommand( $layout, $recordId);
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
	public function add( $data, $layout ){
		
		$findFieldCommand =& $this->fileMaker->newFindCommand( $layout );
	
		//Inserting Record to layout
		$insertRecord =& $this->fileMaker->newAddCommand($layout, $data);
		$result = $insertRecord->execute();
		
		//checking for error
		if (FileMaker::isError($result)) {
			return array( 'error' =>$result->getMessage(), 'code'=> $result.getCode() );
		}
	
		return array('status'=>'Succesfully added a User!', 'code'=>201 );
	}
	
	
	/**
	 * Fetch a User information using the UserID from Database
	 *
	 * @param string $layout contains the layout name
	 * @param string $criteria contains the finding criteria 
	 * @param string $criterion contains the finding criteria argument
	 * @return array() server response
	 */
	public function get( $layout, $criteria, $criterion ){
		
		$findFieldCommand =& $this->fileMaker->newFindCommand( $layout );
		//Specifying field and value to match
		
		$findFieldCommand->addFindCriterion($criteria, $criterion);
		$result = $findFieldCommand->execute();
			
		if(FileMaker::isError($result)){
			return array( 'code'=> $result.getCode() , 'error'=>$result->getMessage());
		}
			
		//Storing the matching record
		$records = $result->getRecords();
		
		//number of records found
		$totalRecords = count($records);
		
		$allRecords = array();

		foreach ($records as $record) { 
		
			$allRecords[  ]=
							
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
	public function authenticateLoginCredentials( $loginCredentials, $layout ){
		
		$email = $loginCredentials['Email'];
		$password = $loginCredentials['Password'];
		
		//using findCriterion command for searching in specific layout
		$findFieldCommand =& $this->fileMaker->newFindCommand( $layout );
		
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
	public function update($layout, $recordId, $data){
		
		$updateRecord =& $this->fileMaker->newEditCommand( $layout, $recordId, $data);
		$result = $updateRecord->execute();
			
		//checking error
		if (FileMaker::isError($result)){
			return array('error'=>$result->getMessage(), 'code'=> $result.getCode() );
		}
			
		return array('status'=>'Succesfully Updated', 'code'=>200 );
	}

	/** 
	 * Get the RecordID according to given criteria
     *
     * @param array() $data contains user data
	 * @return integer $recordId contains record id of particular record
     */
	public function getRecordId($layout, $criteria, $criterion){
		
		$findFieldCommand =& $this->fileMaker->newFindCommand($layout);
		
		$findFieldCommand->addFindCriterion($criteria, $criterion);
		
		$result = $findFieldCommand->execute();
			
		if(FileMaker::isError($result)){
			//return array('error'=>$result->getMessage(), 'code'=> $result.getCode());
			return -1;
		}
			
		//Storing the matching record
		$records = $result->getRecords();
		$record = $records[0];
		$recordId = $record->getRecordId();
		return $recordId;
	}
	
	
}
  
?>