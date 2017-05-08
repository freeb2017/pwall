<?php 

include_once 'model_extension/class.UserModelExtension.php';
include_once 'base_model/class.UserProfileModel.php';
include_once 'business_controller/BaseController.php';
include_once 'helper/DBTransactionManager.php';

/** 
 * The User Controller will handle the 
 * creatiion of users
 * password change
 * and other user level functionalities
 * Username/password creation 
 * 
 * @author nikul
 */
class UserController extends BaseController{
	
	private $type;
	private $UserModel;
	private $UserProfileModel;
		
	public function __construct(){

		parent::__construct();
		
		$this->type  = 'WEB_USER';
		$this->UserModel = new UserModelExtension();
		$this->UserProfileModel = new UserProfileModel();
	}

	/**
	 * returns the user id of newly created users
	 */
	public function getUserId(){
		
		return $this->UserModel->getUserId();
	}
	
	/**
	 * returns the user id of newly created users
	 */
	public function load($user_id){
		
		$this->UserModel->load($user_id);
		return $this->UserModel->getHash();
	}

	/**
	 * returns the user id of newly created users
	 */
	public function loadProfile($user_id){
		
		$this->UserProfileModel->load($user_id);
		return $this->UserProfileModel->getHash();
	}
	
	/**
	 * @TODO Implementation pending
	 */
	public function addUser(array $user_details){
		
		try{
			
			$this->logger->debug('Start of AddUser Flow');

			$transaction_manager = new TransactionManager();
			
			$transaction_manager->beginTransaction();

			if(trim($user_details['email']) == "") {
				throw new Exception('Incorrect email address.');
			}

			if($this->UserModel->checkEmailExists($user_details['email'])) {
				throw new Exception("Email is already in use by other user.");
			}

			if(trim($user_details['dob']) == "") {
				throw new Exception('Please enter your Date of Birth.');
			}

			$UserModel = new UserModel();
			$UserModel->setEmail( $user_details['email'] );
			$UserModel->setPassword( md5($user_details['password']) );
			$UserModel->setIsActive( 1 );
			$UserModel->setCreatedOn( DateUtil::getCurrentDateTime() );
	
			$id = $UserModel->insert();
			
			if(!$id)
				throw new Exception("Error occured. Please try after sometime.");

			$this->logger->debug('@@@Inserted Id: '.$id);
			
			$this->manageUserProfile($user_details, $id);
						
			$transaction_manager->commitTransaction();
			
			return array('SUCCESS', $id);
		}catch (Exception $e){
			
			$transaction_manager->rollbackTransaction();
			return array($e->getMessage(), false);
		}
	}
	
	
	/**
	 * @TODO Implementation pending
	 */
	public function updateUser($user_id, array $user_details){
				
		try{

			$this->logger->debug('Start of UpdateUser Flow');
			
			$transaction_manager = new TransactionManager();
			$transaction_manager->beginTransaction();

			$this->logger->debug('@@@User Details:-'.print_r($user_details , true));
						
			$this->manageUserProfile($user_details, $user_id, true);

			$transaction_manager->commitTransaction();
			
			return 'SUCCESS';

		}catch ( Exception $e ){
			
			$transaction_manager->rollbackTransaction();
			return $e->getMessage();
		}
	}

	public function getCountriesAsOptions($flipped = false) {
		$countries = $this->UserModel->getCountriesAsOptions();
		if($flipped) {
			$countries = array_flip($countries);
		}
		return $countries;
	}

	public function manageUserProfile($params, $user_id, $update_mode = false) {

		//-- integrate with user profile --//
		$UserProfileModel = new UserProfileModel();

		if($update_mode)
			$UserProfileModel->load($user_id);

		$UserProfileModel->setUserId( $user_id );
		$UserProfileModel->setUsername( $params['username'] );
		$UserProfileModel->setGender( $params['gender'] );
		$UserProfileModel->setDob( DateUtil::getMysqlDate($params['dob']) );
		$UserProfileModel->setPhone( $params['phone'] );
		$UserProfileModel->setCountryId( $params['country'] );
		$UserProfileModel->setLikes( addslashes($params['likes']) );
		$UserProfileModel->setDisLikes( addslashes($params['dislikes']) );
		$UserProfileModel->setHobbies( addslashes($params['hobbies']) );
		
		if(!$update_mode) {

			$picture = "/dist/img/avatar_m" . rand(1,2) . ".png";
			if(strtolower($params['gender'])=="female")
				$picture = "/dist/img/avatar_f" . rand(1,2) . ".png";

			$UserProfileModel->setPicture($picture);
			$UserProfileModel->setIsActive(1);
			$UserProfileModel->setCreatedOn( DateUtil::getCurrentDateTime() );
			$id = $UserProfileModel->insert();
		}else {
			$id = $UserProfileModel->update($user_id);
		}

		if( !$id )
			throw new Exception("Error occured. Please try after sometime.");

		if(!$this->manageSkills($user_id, $params['skills'])) {
			throw new Exception("Error occured. Please try after sometime.");
		}
	}

	public function getSkillsAsOptions($flipped = false) {
		$skills = $this->UserModel->getSkillsAsOptions();
		if($flipped) {
			$skills = array_flip($skills);
		}
		return $skills;
	}

	public function manageSkills($user_id, $skills) {
		
		if(!is_array($skills))
			$skills = array($skills);

		$status = 
			$this->UserModel->manageSkills($user_id, $skills);

		if($status) {
			return true;
		}
		return false;
	}

	public function getSkillsByUser($user_id) {
		
		return $this->UserModel->getSkillsByUser($user_id);
	}

	public function getSkillIdsByUser($user_id) {
		$skills = $this->UserModel->getSkillIdsByUser($user_id);
		$skill_ids = array();
		foreach ($skills as $key => $value) {
			array_push($skill_ids,$value['quality_id']);
		}
		return $skill_ids;
	}
}
?>