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
	public $logger;
    
	/**
	 * Contructor establish connection with FileMaker Server
	 *
	 */
	function __construct($fmFile, $fmHost, $fmUser, $fmPassword, $logger){
		require_once("FileMaker.php");
		
		$this->logger = $logger;

		// Establish connection
		$this->fileMaker = new FileMaker( $fmFile, $fmHost, $fmUser, $fmPassword);
		$dbs = $this->fileMaker->listDatabases();
	
		// Checking connection to server works without user credits
		if(FileMaker::isError($dbs)) { 
			
			$this->error = $dbs->getMessage().' , '.$dbs->getCode(); 

			$this->logger->addInfo($dbs->getMessage().' , '.$dbs->getCode());
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
			
			$response = array('error'=>$result->getMessage(), 'code'=> $result->getCode());
		
			$this->logger->addInfo(json_encode($response));
			return $response;
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
	
		// Inserting Record to layout
		$insertRecord =& $this->fileMaker->newAddCommand($layout, $data);
		$result = $insertRecord->execute();
		
		// checking for error
		if (FileMaker::isError($result)) {

			$response = array( 'error' =>$result->getMessage(), 'code'=> $result->getCode() );
			
			$this->logger->addInfo(json_encode($response));
			return $response;
		}
	
		return array('status'=>'New record added successfully!', 'code'=>201 );
	}
	
	
	/**
	 * Fetch a User information using the UserID from Database
	 *
	 * @param string $layout contains the layout name
	 * @param array() $criteriaData contains the find criteria 
	 * @return array() server response
	 */
	public function get( $layout, $criteriaData ){
		
		$findFieldCommand =& $this->fileMaker->newFindCommand($layout);
		
		foreach( $criteriaData as $criteria => $criterion ){
			
			$findFieldCommand->addFindCriterion($criteria, $criterion);
		}

		$result = $findFieldCommand->execute();
			
		if(FileMaker::isError($result)){
			$response = array( 'code'=> $result->getCode() , 'error'=>$result->getMessage());
			
			$this->logger->addInfo(json_encode($response));
			return $response;
		}
			
		// Storing the matching record
		$records = $result->getRecords();
		
		$foundCount = $result->getFoundSetCount();

		// number of records found
		$totalRecords = count($records);

		
		$allRecords = array();

		foreach ($records as $record) { 
			
			
			$allRecords[  ]=
							
							$singleRecord = array(
								
								'___kp_UserID' => $record->getField('___kp_UserID'),
								'FirstName' => $record->getField('FirstName'),
								'LastName' => $record->getField('LastName'),
								'Name' => $record->getField('Name'),
								'_ka_Email' => $record->getField('_ka_Email'),
								'_ka_Mobile' => $record->getField('_ka_Mobile'),
								'Designation' => $record->getField('Designation'),
								'Address' => $record->getField('Address'),
								'ResultsFetch' => $totalRecords,
								'ResultsFound' => $foundCount
							
							);
						
	
		} 

		return $allRecords;
	}

	/**
	 * Fetch a User information using the Filter keyword from Database
	 *
	 * @param string $layout contains the layout name
	 * @param array() $criteriaData contains the find criteria 
	 * @return array() server response
	 */
	public function getFiltered( $layout, $criteriaData ){
		
		$count = 1;
		// Creating a Compound find instance 
		$compoundFind = $this->fileMaker->newCompoundFindCommand($layout);
		
		foreach( $criteriaData as $criteria => $criterion ){
			
			$findFieldCommand =& $this->fileMaker->newFindRequest($layout);
			$findFieldCommand->addFindCriterion($criteria, $criterion);

			// Adding find criteria in CompountFind 
			$compoundFind->add($count, $findFieldCommand);
			$count++;
		}
		
		$result = $compoundFind->execute();
			
		if(FileMaker::isError($result)){
			$response = array( 'code'=> $result->getCode() , 'error'=>$result->getMessage());
			
			$this->logger->addInfo(json_encode($response));
			return $response;
		}
			
		// Storing the matching record
		$records = $result->getRecords();
		
		// number of records found
		$totalRecords = count($records);
		
		$allRecords = array();

		foreach ($records as $record) { 
			
			
			$allRecords[]=
							
							$singleRecord = array(
								
								'___kp_UserID' => $record->getField('___kp_UserID'),
								'FirstName' => $record->getField('FirstName'),
								'LastName' => $record->getField('LastName'),
								'Name' => $record->getField('Name'),
								'_ka_Email' => $record->getField('_ka_Email'),
								'_ka_Mobile' => $record->getField('_ka_Mobile'),
								'Designation' => $record->getField('Designation'),
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
		
		$email = $loginCredentials['_ka_Email'];
		$password = $loginCredentials['Password'];
		
		// using findCriterion command for searching in specific layout
		$findFieldCommand =& $this->fileMaker->newFindCommand( $layout );
		
		$findFieldCommand->addFindCriterion("_ka_Email",'=="'.$email . '"');
		
		$result = $findFieldCommand->execute();

		if(FileMaker::isError($result)){
			$response = array('code'=>$result->getCode(), 
			'error'=>$result->getMessage(), 
			'status'=>"Login failed ! invalid credentials"
			);

			$this->logger->addInfo(json_encode($response));
			return $response;
		}
			
		// Storing the matching record
		$records = $result->getRecords();
		$record = $records[0];
		$pswrd = $record->getField("Password");

		if(strcmp($password,$pswrd)!=0){
			$responseError = array('code'=>401,
			 'error'=>'Password is incorrect',
			 'status'=>"Login failed ! invalid credentials"
			);

			$this->logger->addInfo(json_encode($responseError));
			return $responseError;
		}
		
		return array(
			'code'=>200, 
			'UserID'=>$record->getField("___kp_UserID"),
			'error'=>'',
			'status'=>'Logged In Successfully'
		);
		
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
			
		// checking error
		if (FileMaker::isError($result)){
			$response = array('error'=>$result->getMessage(), 'code'=> $result->getCode() );
			
			$this->logger->addInfo(json_encode($response));
			return $response;
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
			
			$this->logger->addInfo("Couldn't get the record Id");
			return -1;
		}
			
		// Storing the matching record
		$records = $result->getRecords();
		$record = $records[0];
		$recordId = $record->getRecordId();
		return $recordId;
	}
	

	/**
	 * Fetch all activities from Database
	 *
	 * @param string $layout contains the layout name
	 * @param array() $criteriaData contains the find criteria 
	 * @return array() server response
	 */
	public function getActivity($layout, $criteriaData){

		
		$findFieldCommand =& $this->fileMaker->newFindCommand($layout);
		
		foreach( $criteriaData as $criteria => $criterion ){
			
			$findFieldCommand->addFindCriterion($criteria, $criterion);
		}
		
		$result = $findFieldCommand->execute();
			
		if(FileMaker::isError($result)){
			$response = array( 'code'=> $result->getCode() , 'error'=>$result->getMessage());
		
			$this->logger->addInfo(json_encode($response));
			return $response;
		}
			
		// Storing the matching record
		$records = $result->getRecords();
		
		// number of records found
		$totalRecords = count($records);
		
		$allRecords = array();

		foreach ($records as $record) { 
			
			
			$allRecords[]=
							
							$singleRecord = array(
								
								'___kp_Id' => $record->getField('___kp_Id'),
								'__kf_UserID' => $record->getField('__kf_UserID'),
								'Name' => $record->getField('Name'),
								'CreationTimestamp' => $record->getField('CreationTimestamp'),
								'CreatedBy' => $record->getField('CreatedBy'),
								'ModificationTimestamp' => $record->getField('ModificationTimestamp'),
								'ModifiedBy' => $record->getField('ModifiedBy')
							
							);
						
	
		} 

		return $allRecords;
	}

	/**
	 * Filter and Fetch all activities from Database
	 *
	 * @param string $layout contains the layout name
	 * @param array() $criteriaData contains the find criteria 
	 * @return array() server response
	 */
	public function filterActivity($layout, $userID, $criteriaData){

		$count = 1;

		// Creating a Compound find instance 
		$compoundFind = $this->fileMaker->newCompoundFindCommand($layout);

		foreach( $criteriaData as $criteria => $criterion ){
			
			$findFieldCommand =& $this->fileMaker->newFindRequest($layout);
			$findFieldCommand->addFindCriterion($criteria, $criterion);
			$findFieldCommand->addFindCriterion('__kf_UserID', $userID);

			// Adding find criteria in CompountFind 
			$compoundFind->add($count, $findFieldCommand);
			$count++;
		}

		$result = $compoundFind->execute();
			
		if(FileMaker::isError($result)){
			
			$response = array( 'code'=> $result->getCode() , 'error'=>$result->getMessage());
			$this->logger->addInfo(json_encode($response));
			
			return $response;
		}
			
		// Storing the matching record
		$records = $result->getRecords();
		
		// number of records found
		$totalRecords = count($records);
		
		$allRecords = array();

		foreach ($records as $record) { 
			
			$allRecords[]=
							
							$singleRecord = array(
								
								'___kp_Id' => $record->getField('___kp_Id'),
								'__kf_UserID' => $record->getField('__kf_UserID'),
								'Name' => $record->getField('Name'),
								'CreationTimestamp' => $record->getField('CreationTimestamp'),
								'CreatedBy' => $record->getField('CreatedBy'),
								'ModificationTimestamp' => $record->getField('ModificationTimestamp'),
								'ModifiedBy' => $record->getField('ModifiedBy')
							
							);
						
	
		} 

		return $allRecords;
	}
}
  
?>