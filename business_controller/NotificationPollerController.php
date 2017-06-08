<?php 

include_once 'model_extension/class.NotificationsModelExtension.php';

/** 
 * The Notification Poller Controller will handle the 
 * user level poller of different types of notifications based on some defined format
 * 
 * @author nikul
 */
class NotificationPollerController{
	
	private $logger;
	private $user_id;
	private $NotificationModel;
		
	public function __construct(){

		global $logger, $currentuser;

		$this->logger = $logger;
		$this->user_id = $currentuser->getUserId();

		$this->NotificationModel = new NotificationsModelExtension();
	}

	public function pullNotificationAlerts($user_id, $last_id = false){
		$this->logger->debug("Start of Pull notification Alerts: ".$user_id.", Last Id:".$last_id);
		$alerts = $this->NotificationModel->getUserNotificationsAlerts($user_id, $last_id);
		$this->logger->debug("End of Pull notification Alerts:".print_r($alerts, true));
		return $alerts;
	}

	public function pullNotificationFeeds($user_id, $last_id = false){
		$this->logger->debug("Start of Pull notification Feeds: ".$user_id.", Last Id:".$last_id);
		$alerts1 = $this->NotificationModel->getUserNotificationsFeeds($user_id, $last_id);
		$alerts2 = $this->pullFriendRatedFriendAlert($user_id, $last_id);
		if($alerts2)
			$result = array_merge($alerts1, $alerts2);
		else
			$result = $alerts1;
		$this->logger->debug("End of Pull notification Alerts:".print_r($result, true));
		return $result;
	}

	private function pullFriendRatedFriendAlert($user_id, $last_id = false){
		$this->logger->debug("Start of Pull F2F rated notification Feeds: ".$user_id.", Last Id:".$last_id);
		
		$friends = $this->NotificationModel->getActiveFriends($user_id);
		if(count($friends) < 1)
			return array();

		$this->logger->debug("Friends: ".print_r($friends, true));

		$user_ids = array();
		foreach ($friends as $friend) {
			if($friend["user_one_id"] == $user_id)
				array_push($user_ids, $friend["user_two_id"]);
			else if($friend["user_two_id"] == $user_id)
				array_push($user_ids, $friend["user_one_id"]);
		}

		$alerts = $this->NotificationModel->getUserNotificationsF2FFeeds($user_ids, $last_id);
		$this->logger->debug("End of Pull F2F rated notification Alerts:".print_r($alerts, true));
		return $alerts;
	}

	private function pullFriendChangedPicAlert($user_id, $last_id = false){
		$this->logger->debug("Start of Pull Friend changed pic notification Feeds: ".$user_id.", Last Id:".$last_id);
		
		$friends = $this->NotificationModel->getActiveFriends($user_id);
		if(count($friends) < 1)
			return array();

		$this->logger->debug("Friends: ".print_r($friends, true));

		$user_ids = array();
		foreach ($friends as $friend) {
			if($friend["user_one_id"] == $user_id)
				array_push($user_ids, $friend["user_two_id"]);
			else if($friend["user_two_id"] == $user_id)
				array_push($user_ids, $friend["user_one_id"]);
		}

		$alerts = $this->NotificationModel->getUserNotificationsPicChangedFeeds($user_ids, $last_id);
		$this->logger->debug("End of Pull F2F rated notification Alerts:".print_r($alerts, true));
		return $alerts;
	}

	public function updateNotificationAlerts($ids){
		$this->NotificationModel->updateNotificationAlerts($ids);
	}

	public function getUnreadAlertsCount($user_id){
		$count = $this->NotificationModel->getUnreadNotificationAlerts($user_id);
		return $count;
	}
}
?>