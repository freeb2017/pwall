<?php 

include_once 'model_extension/class.UserModelExtension.php';
include_once 'base_model/class.UserProfileModel.php';
include_once 'business_controller/BaseController.php';
include_once 'helper/DBTransactionManager.php';
include_once 'business_controller/NotificationPusherController.php';
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
	private $NotificationPusher;
	private $UserModel;
	private $UserProfileModel;
		
	public function __construct(){

		parent::__construct();

		$this->type  = 'WEB_USER';
		$this->UserModel = new UserModelExtension();
		$this->UserProfileModel = new UserProfileModel();
		$this->NotificationPusher = new NotificationPusherController();
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

			// $transaction_manager = new TransactionManager();
			
			// $transaction_manager->beginTransaction();

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
						
			// $transaction_manager->commitTransaction();
			
			return array('SUCCESS', $id);
		}catch (Exception $e){
			
			// $transaction_manager->rollbackTransaction();
			return array($e->getMessage(), false);
		}
	}
	
	
	/**
	 * @TODO Implementation pending
	 */
	public function updateUser($user_id, array $user_details){
				
		try{

			$this->logger->debug('Start of UpdateUser Flow');
			
			// $transaction_manager = new TransactionManager();
			// $transaction_manager->beginTransaction();

			$this->logger->debug('@@@User Details:-'.print_r($user_details , true));
						
			$this->manageUserProfile($user_details, $user_id, true);

			// $transaction_manager->commitTransaction();
			
			return 'SUCCESS';

		}catch ( Exception $e ){
			
			// $transaction_manager->rollbackTransaction();
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
		$UserProfileModel->setIsActive(1);
		$UserProfileModel->setCreatedOn( DateUtil::getCurrentDateTime() );
		$UserProfileModel->setLastModifiedOn( DateUtil::getCurrentDateTime() );

		if(!$update_mode) {

			$picture = "/dist/img/avatar_m" . rand(1,2) . ".png";
			if(strtolower($params['gender'])=="female")
				$picture = "/dist/img/avatar_f" . rand(1,2) . ".png";

			$UserProfileModel->setPicture($picture);
			$id = $UserProfileModel->insert();
		}else {
			$id = $UserProfileModel->update($user_id);
		}

		if( !$id )
			throw new Exception("Error occured on update. Please try after sometime.");

		if(!$update_mode)
		   return;

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

		$skills = implode(",", $skills);

		$status = 
			$this->UserModel->deleteSuggestedQualities($user_id, $skills);

		return true;
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

	public function getUserListDetails($user_id, $include = false){
		return $this->UserModel->getUserListDetails(
			$user_id, $include);
	}

	public function getFriendsList($user_id){
		return $this->UserModel->getFriendsList(
			$user_id);
	}

	public function insertFriendRequest(
			$user_one_id, $user_two_id, $user_action_id
		){
		
		$this->logger->debug(
			'Start of Insert Friend Request flow u1:'
			.$user_one_id.', u2:'.$user_two_id.', action user:'.$user_action_id
		);

		$status = $this->UserModel->insertFriendRequest(
					$user_one_id, $user_two_id, $user_action_id
				);

		if($status > 0){
			$this->NotificationPusher->addPushNotification(
				"FRIEND_REQUEST_SENT",
				array("user_id" => $user_two_id)
			);
			return true;
		}

		return false;
	}

	public function updateFriendRequest(
			$user_one_id, $user_two_id, $user_action_id, $status
		){
		
		$this->logger->debug(
			'Start of Update Friend Request flow u1:'
			.$user_one_id.', u2:'.$user_two_id.', action user:'.$user_action_id.', status: '.$status
		);

		$result = $this->UserModel->updateFriendRequest(
			$user_one_id, $user_two_id, $user_action_id, $status
		);

		if($result > 0){
			$user = $user_two_id;
			if($user_action_id != $user_one_id)
				$user = $user_one_id;

			if($status == "ACCEPTED") {
				$this->NotificationPusher->addPushNotification(
					"FRIEND_REQUEST_ACCEPTED",
					array("user_id" => $user)
				);
			}
			else if($status == "DECLINED") {
				$this->NotificationPusher->addPushNotification(
					"FRIEND_REQUEST_REJECTED",
					array("user_id" => $user)
				);
			}
			return true;
		}

		return false;
	}

	public function checkFriendshipStatus($user_one_id, $user_two_id, $status = false){
		
		$this->logger->debug(
			'Start of check Friend Request u1:'
			.$user_one_id.', u2:'.$user_two_id.', status: '.$status
		);
		
		return $this->UserModel->checkFriendshipStatus(
			$user_one_id, $user_two_id, $status);
	}

	public function getActiveFriendsCount($user_id){
		$count = $this->UserModel->getActiveFriendsCount($user_id);
		if(!$count)
			$count = 0;
		return $count;
	}

	public function getOverallRatingsBySkill($user_id){
		return $this->UserModel->getOverallRatingsBySkill($user_id);
	}

	public function getRatingsByRatedBy($user_id, $rated_by){
		return $this->UserModel->getRatingsByRatedBy($user_id, $rated_by);
	}

	public function getMergedRatingListByRatedBy($user_id, $rated_by){
		$overall_ratings = $this->getOverallRatingsBySkill($user_id);
		$ratings = $this->getRatingsByRatedBy($user_id, $rated_by);

		$rating_per_skill = array();
		if($overall_ratings){
			foreach ($overall_ratings as $value) {
				$temp = $value;
				if($ratings){
					foreach ($ratings as $ivalue) {
						if($value['quality_id'] == $ivalue['quality_id']){
							$temp['rating'] = $ivalue['rating'];
							$temp['rate_label'] = $ivalue['rate_label'];
							break;
						}
					}
				}
				array_push($rating_per_skill,$temp);
			}
		}
		return $rating_per_skill;
	}

	public function rateQuality($user_id, $quality_id, $rated_by, $rate_id){

		$this->logger->debug(
			'Start of rate Friend u1:'
			.$user_id.', u2:'.$quality_id.', rate: '.$rate_id
		);

		$status = $this->UserModel->manageRatingPerSkill(
			$user_id, $quality_id, $rated_by, $rate_id
		);

		if(!$status)
			return false;

		$this->NotificationPusher->addPushNotification(
					"RATED_ME",
					array(
						"user_id" => $user_id,
						"quality_id" => $quality_id
					)
				);

		$this->NotificationPusher->addPushNotification(
					"FRIEND_RATED_FRIEND",
					array(
						"user_id" => $rated_by,
						"fuser_id" => $user_id,
						"quality_id" => $quality_id
					)
				);

		$result = $this->UserModel->getRatingBySkill($user_id, $quality_id);

		return $result;
	}

	public function getRatingsConversion($rating){
		if(!$rating) return "Not Rated";
		$ratings = $this->UserModel->getRatingsAsOptions();
		$ratings = array_flip($ratings);

		$rating_desc = $ratings[$rating];

		if($rating_desc) return $rating_desc;

		if($rating >= 4.5 && $rating <= 5)
			$rating_desc = $ratings[5];
		else if($rating >= 3.5 && $rating < 4.5)
			$rating_desc = $ratings[4];
		else if($rating >= 2.5 && $rating < 3.5)
			$rating_desc = $ratings[3];
		else if($rating >= 1.5 && $rating < 2.5)
			$rating_desc = $ratings[2];
		else 
			$rating_desc = $ratings[1];

		return $rating_desc;
	}

	public function getRatingsThemeConversion($rating){
		if(!$rating) return "bg-gray-active";

		if($rating >= 4.5 && $rating <= 5)
			$rating_desc = "bg-green-active";
		else if($rating >= 3.5 && $rating < 4.5)
			$rating_desc = "bg-purple";
		else if($rating >= 2.5 && $rating < 3.5)
			$rating_desc = "bg-teal-active";
		else if($rating >= 1.5 && $rating < 2.5)
			$rating_desc = "bg-yellow-active";
		else 
			$rating_desc = "bg-red-active";

		return $rating_desc;
	}

	public function manageSuggestions($user_id, $quality_id, $suggested_by){
		$this->logger->debug(
			'Start of manage suggestions:'
			.$user_id.', u2:'.$quality_id.', suggested_by: '.$suggested_by
		);

		$status = $this->UserModel->manageSuggestions(
			$user_id, $quality_id, $suggested_by
		);

		if(!$status)
			return false;

		if($quality_id){
			$qualities = explode(",", $quality_id);
			foreach ($qualities as $key) {
				$this->NotificationPusher->addPushNotification(
					"SUGGEST_QUALITY",
					array(
						"user_id" => $user_id,
						"quality_id" => $key
					)
				);
			}
		}	

		return $status;
	}

	public function getSuggestedList($user_id, $suggested_by){
		$this->logger->debug(
			'Start of manage suggestions:'
			.$user_id.', suggested_by: '.$suggested_by
		);

		$skills = $this->UserModel->getSuggestedList(
			$user_id, $suggested_by
		);

		$skill_ids = array();
		foreach ($skills as $key => $value) {
			array_push($skill_ids,$value['quality_id']);
		}
		return $skill_ids;
	}

	public function getSuggestedSkillsByFriends($user_id){
		return $this->UserModel->getSuggestedSkillsByFriends($user_id);
	}

	public function updateSuggestions($user_id, $quality_id, $is_active){
		$this->logger->debug(
			'Start of update suggestions:'
			.$user_id.', quality_id: '.$quality_id
		);

		$status = $this->UserModel->updateSuggestions(
			$user_id, $quality_id, $is_active);

		if($is_active){
			$skills = array($quality_id);
			$status = $this->UserModel->manageSkills($user_id, $skills, true);
			return $status;
		}

		return $status;
	}

	public function filterSuggestedQualities($user_id, $skills){
		$suggested_skills = $this->UserModel->filterSuggestedQualities();
		if($suggested_skills){
			foreach ($suggested_skills as $value) {
				unset($skills[$value['quality_id']]);
			}
		}
		return $skills;
	}

	public function getRateCountsByUser($user_id){

		$result = $this->UserModel->getRateCountsByUser($user_id);

		return $result;
	}

	public function getStatesAsOptions() {

		$result = $this->UserModel->getStatesAsOptions();

		return $result;
	}

	public function getCitiesAsOptions() {

		$result = $this->UserModel->getCitiesAsOptions();

		return $result;
	}

	public function changePassword($currentpassword, $password, $user_id) {

		$profile = $this->load($user_id);

		if($profile['password'] !== md5($currentpassword)) {
			throw new Exception("Current Password does not match. Please try again.");
		}

		if(!$password)
			throw new Exception("Password must not be blank.");

		$password = md5($password);

		$result = $this->UserModel->changePassword($password, $user_id);

		return $result;
	}

	public function forgetPassword($params) {

		if(!$params['email']) {
			throw new Exception("Email must not be blank. Please try again.");
		}

		$user = $this->UserModel->getUserByEmail($params['email']);
		$profile = $this->loadProfile($user['user_id']);

		$password = $params["password"];
		$confirmpassword = $params["confirm_password"];

		if($params['email'] !== $user['email']) {
			throw new Exception("Email does not match. Please try again.");
		}

		if($profile['dob'] !== $params['dob']) {
			throw new Exception("Date of Birth does not match. Please try again.");
		}

		if($password !== $confirmpassword) {
			throw new Exception("Both Passwords do not match. Please try again.");
		}

		if(!$password || !$confirmpassword)
			throw new Exception("Password must not be blank.");

		$password = md5($password);

		$result = $this->UserModel->changePassword($password, $user['user_id']);

		return $result;
	}

	public function changePicture($params, $user_id) {

		$validextensions = array("jpeg", "jpg", "png");
		$temporary = explode(".", $params["picture"]["name"]);
		$file_extension = end($temporary);

		if ((
				($params["picture"]["type"] == "image/png") 
					|| 
				($params["picture"]["type"] == "image/jpg") 
					|| 
				($params["picture"]["type"] == "image/jpeg")
			) 
			&& 
			($params["picture"]["size"] < 250000)//Approx. 250kb files can be uploaded.
			&& in_array($file_extension, $validextensions)
		) {

			if ($params["picture"]["error"] > 0) {
				throw new Exception("Error: ".$params["picture"]["error"]);
			} else {
				if (file_exists("images/" . $params["picture"]["name"])) {
					throw new Exception("Error: ".$params["picture"]["name"] . " <b>already exists.</b>");
				} else {
					$imageFullpath = "images/".time().Util::uglify($params["picture"]["name"]);
					move_uploaded_file($params["picture"]["tmp_name"], $imageFullpath);
					$this->logger->debug("Image uploaded: /".$imageFullpath);
					$result = $this->UserModel->changePicture("/".$imageFullpath, $user_id);
				}
			}
		} else {
			throw new Exception("Error: ***Invalid file Size or Type***");
		}

		$this->NotificationPusher->addPushNotification(
			"FRIEND_CHANGED_PIC",
			array("user_id" => $user_id)
		);

		return $result;
	}

	public function getPercentageToImproveUpon($user_id) {

		$result = $this->UserModel->getPercentageToImproveUpon($user_id);

		return $result;
	}

	public function getOverallResultByQualities($user_id) {

		$result = $this->UserModel->getOverallResultByQualities($user_id);

		return $result;
	}
}
?>