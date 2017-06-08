<?php
/**
 * Pwall Ajax service support
 * 
 * @author nikul
 */
class PwallAjaxService extends BaseAjaxService{
	
	private $user_id;
	private $UserController;
	private $NotificationPollerController;

	public function __construct($type){
		
		global $currentuser;

		parent::__construct($type);

		$this->user_id = $currentuser->getUserId();
    	$this->UserController = new UserController();
    	$this->NotificationPollerController = new NotificationPollerController();
	}
	
	public function process(){
		
		$this->logger->debug( 'Checking For Type : ' . $this->type );
		
		switch ( $this->type ){
			
			case 'make_friend' :
				
				$this->addAsFriend();
				break;

			case 'manage_friendship' :

				$this->manageFriendShip();
				break;

			case 'rate_quality' :
				$this->rateQuality();
				break;

			case 'manage_suggestions' :
				$this->manageSuggestions();
				break;

			case 'suggest_status':
				$this->updateSuggestions();
				break;

			case 'pull_alerts':
				$this->pullAlerts();
				break;

			case 'pull_feeds':
				$this->pullFeeds();
				break;

			case 'read_alerts':
				$this->markReadAlerts();
				break;
		}
	}

	private function addAsFriend() {
		$this->logger->debug('Start of Add as friend method: '.print_r($_GET, true));
		
		$user_one_id = $this->user_id;
		$user_two_id = $_GET['user_id'];
		$user_action_id = $this->user_id;

		$this->data["status"] = 
			$this->UserController->insertFriendRequest(
				$user_one_id, $user_two_id, $user_action_id
			);

		$this->logger->debug('End of add as friend method: '.print_r($_GET, true));
	}

	private function manageFriendShip() {
		$this->logger->debug('Start of manage friendship method: '.print_r($_GET, true));
		
		$user_one_id = $_GET['user1'];
		$user_two_id = $_GET['user2'];
		$user_action_id = $this->user_id;
		$status = $_GET['status'];

		$this->data["status"] = 
			$this->UserController->updateFriendRequest(
				$user_one_id, $user_two_id, $user_action_id, $status
			);

		$this->logger->debug('End of manage friendship method: '.print_r($_GET, true));
	}

	private function rateQuality() {
		$this->logger->debug('Start of rate skill method: '.print_r($_GET, true));
		
		$quality_id = $_GET['qid'];
		$user_id = $_GET['uid'];
		$rated_by = $this->user_id;
		$rate_id = $_GET['rating'];

		$result = 
			$this->UserController->rateQuality($user_id, $quality_id, $rated_by, $rate_id);

		if($result){
			$this->data["status"] = true;
			$this->data["info"] = $result;
		}else{
			$this->data["status"] = false;
		}

		$this->logger->debug('End of rate method: '.print_r($_GET, true));
	}

	private function manageSuggestions() {
		$this->logger->debug('Start of manage suggestions method: '.print_r($_GET, true));
		
		$user_id = $_GET['user_id'];
		$quality_id = $_GET['quality_id'];
		$suggested_by = $this->user_id;

		$this->data["status"] = 
			$this->UserController->manageSuggestions(
				$user_id, $quality_id, $suggested_by
			);

		$this->logger->debug('End of manage suggestions method: '.print_r($_GET, true));
	}

	private function updateSuggestions() {
		$this->logger->debug('Start of accept/reject suggestions method: '.print_r($_GET, true));
		
		$user_id = $this->user_id;
		$quality_id = $_GET['quality_id'];
		$is_active = $_GET['is_active'];

		$this->data["status"] = 
			$this->UserController->updateSuggestions(
				$user_id, $quality_id, $is_active
			);

		$this->logger->debug('End of accept/reject suggestions method: '.print_r($_GET, true));
	}

	private function pullFeeds() {
		$this->logger->debug('Start of pull feeds method: '.print_r($_GET, true));
		
		$user_id = $this->user_id;
		$last_id = $_GET["lid"];

		$this->data["feeds"] = 
			$this->NotificationPollerController->pullNotificationFeeds(
				$user_id, $last_id
			);

		$this->data["status"] = true;

		$this->logger->debug('End of pull feeds method: '.print_r($_GET, true));
	}

	private function pullAlerts() {
		$this->logger->debug('Start of pull alerts method: '.print_r($_GET, true));
		
		$user_id = $this->user_id;
		$last_id = $_GET["lid"];

		$this->data["alerts"] = 
			$this->NotificationPollerController->pullNotificationAlerts(
				$user_id, $last_id
			);

		$this->data['count'] = $this->NotificationPollerController->getUnreadAlertsCount($user_id);

		$this->data["status"] = true;

		$this->logger->debug('End of pull alerts method: '.print_r($_GET, true));
	}

	private function markReadAlerts() {

		$this->logger->debug('Start of markReadAlerts method: '.print_r($_GET, true));
		
		$ids = $_GET["ids"];

		$this->NotificationPollerController->updateNotificationAlerts(
				$ids
			);

		$this->data["status"] = true;

		$this->logger->debug('End of markReadAlerts method: '.print_r($_GET, true));
	}
}
?>