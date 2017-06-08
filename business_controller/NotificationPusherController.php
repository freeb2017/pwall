<?php 
include_once 'model_extension/class.NotificationsModelExtension.php';
include_once 'base_model/class.UserProfileModel.php';
include_once 'model_extension/class.UserModelExtension.php';

/** 
 * The Notification Pusher Controller will handle the 
 * creation of different types of notifications for any users  based on some defined format
 * 
 * @author nikul
 */
class NotificationPusherController{
	
	private $logger;
	private $user_id;
	private $profile;
	private $qualities;
	private $notifications;
	private $NotificationModel;
		
	public function __construct(){

		global $logger, $currentuser;

		$this->logger = $logger;

		if($currentuser)
			$this->user_id = $currentuser->getUserId();
		
		$this->NotificationModel = new NotificationsModelExtension();
		$this->notifications = $this->NotificationModel->getNoticationsAsOptions();

		$this->profile = $this->loadProfile($this->user_id);
		$this->qualities = $this->getSkillsAsOptions(true);
	}

	private function loadProfile($user_id){
		$UserProfileModel = new UserProfileModel();
		$UserProfileModel->load($user_id);
		return $UserProfileModel->getHash();
	}

	public function getSkillsAsOptions($flipped = false) {
		$UserModel = new UserModelExtension();
		$skills = $UserModel->getSkillsAsOptions();
		if($flipped) {
			$skills = array_flip($skills);
		}
		return $skills;
	}

	public function addPushNotification($type, $data){
		$this->logger->debug("Start of Add notification to DB: ".$type.', Data: '.print_r($data, true));
		
		$data['notification_id'] = $this->notifications[strtoupper($type)];
		$data['metadata'] =  $this->NotificationModel->getNoticationMetaById($data['notification_id']);

		switch(strtoupper($type)) {
			case 'RATED_ME':
				$alert = $this->pushRatedMeAlert($data);
				break;
			case 'SUGGEST_QUALITY':
				$alert = $this->pushSuggestQualityAlert($data);
				break;
			case 'FRIEND_REQUEST_ACCEPTED':
				$alert = $this->pushFriendRequestAcceptedAlert($data);
				break;
			case 'FRIEND_REQUEST_REJECTED':
				$alert = $this->pushFriendRequestRejectedAlert($data);
				break;
			case 'FRIEND_REQUEST_SENT':
				$alert = $this->pushRequestSentAlert($data);
				break;
			case 'FRIEND_RATED_FRIEND':
				$alert = $this->pushFriendRatedFriendAlert($data);
				break;
			case 'FRIEND_CHANGED_PIC':
				$alert = $this->pushFriendChangedPicAlert($data);
				break;
		}

		$this->NotificationModel->addUserNotification(
			$data['user_id'], $data['notification_id'], $alert
		);

		$this->logger->debug("End of Add notification to DB");
	}

	private function pushRatedMeAlert($data){

		$this->logger->debug("Start of pushRatedMeAlert: ".print_r($data, true));
		
		$notication_id = $data["metadata"]["id"];
		$alert = $data["metadata"]["format"];
		$quality = $this->qualities[$data["quality_id"]];
		$alert = Util::templateReplace($alert, 
					array(
						"user" => $this->profile["username"], 
						"quality" => $quality
					));
		return $alert;
	}

	private function pushSuggestQualityAlert($data){

		$this->logger->debug("Start of pushSuggestQualityAlert: ".print_r($data, true));
		
		$notication_id = $data["metadata"]["id"];
		$alert = $data["metadata"]["format"];
		$quality = $this->qualities[$data["quality_id"]];
		$alert = Util::templateReplace($alert, 
					array(
						"user" => $this->profile["username"], 
						"quality" => $quality
					));

		return $alert;
	}

	private function pushFriendRequestAcceptedAlert($data){

		$this->logger->debug("Start of pushFriendRequestAcceptedAlert: ".print_r($data, true));
		
		$notication_id = $data["metadata"]["id"];
		$alert = $data["metadata"]["format"];
		$alert = Util::templateReplace($alert, 
					array("user" => $this->profile["username"]));

		return $alert;
	}

	private function pushFriendRequestRejectedAlert($data){

		$this->logger->debug("Start of pushFriendRequestRejectedAlert: ".print_r($data, true));
		
		$notication_id = $data["metadata"]["id"];
		$alert = $data["metadata"]["format"];
		$alert = Util::templateReplace($alert, 
					array("user" => $this->profile["username"]));

		return $alert;
	}

	private function pushRequestSentAlert($data){
		
		$this->logger->debug("Start of pushRequestSentAlert: ".print_r($data, true));
		
		$notication_id = $data["metadata"]["id"];
		$alert = $data["metadata"]["format"];
		$alert = Util::templateReplace($alert, 
					array("user" => $this->profile["username"]));

		return $alert;
	}

	private function pushFriendRatedFriendAlert($data){
		
		$this->logger->debug("Start of pushFriendRatedFriendAlert: ".print_r($data, true));
		
		$notication_id = $data["metadata"]["id"];
		$alert = $data["metadata"]["format"];
		$quality = $this->qualities[$data["quality_id"]];
		$user1 = $this->loadProfile($data["fuser_id"]);
		$user1 = $user1["username"];
		$alert = Util::templateReplace($alert, 
					array("user1" => $user1, 
						"quality" => $quality,
						"user2" => $this->profile["username"]
					));

		return $alert;
	}

	private function pushFriendChangedPicAlert($data){
		
		$this->logger->debug("Start of pushFriendChangedPicAlert: ".print_r($data, true));
		
		$notication_id = $data["metadata"]["id"];
		$alert = $data["metadata"]["format"];
		$alert = Util::templateReplace($alert, 
					array("user" => $this->profile["username"]));

		return $alert;
	}
}
?>