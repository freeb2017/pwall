<?php
/**
 * @author nikul
 * In this class contains notification level db queries
 */
class NotificationsModelExtension{
	
	private $logger;
	private $cuser_id;
	private $database;

	public function __construct(){

		global $logger;
		
		$this->logger = $logger;
		
		// $this->database = new Dbase('pwall');
		$this->database = Dbase::getInstance();
	}

	public function addUserNotification($user_id, $notification_id, $content){

		$content = addslashes($content);

		$dtime = DateUtil::getCurrentDateTime();

		$sql = "INSERT INTO `user_notifications` 
					(user_id, notification_id, content, created_on)
				VALUES
					($user_id, $notification_id, '$content', '$dtime')";
		
		return $this->database->insert($sql);
	}

	public function getNoticationsAsOptions(){
		$sql = 'SELECT `id`, `activity` FROM `notifications`';
		return $this->database->query_hash($sql, "activity", "id");
	}

	public function getNoticationMetaById($id){
		
		$sql = "SELECT * 
					FROM `notifications` 
				WHERE `id` = $id";

		return $this->database->query_firstrow($sql);
	}

	public function getUserNotificationsAlerts($user_id, $last_id){

		$filter = "";
		if($last_id)
			$filter = "AND un.`id` > '$last_id'";

		$sql = "SELECT 
					un.`id`, 
					un.`user_id`, 
					un.`notification_id`, 
					un.`content`,
					DATE_FORMAT(un.`created_on`, '%D %M %Y %r') as 'created_on',
					n.`activity`
				FROM `user_notifications` un 
					JOIN `notifications` n ON n.`id` = un.`notification_id`
				WHERE un.`user_id` = '$user_id' $filter AND n.`priority` = 'HIGH' ORDER BY un.`id` DESC";

		return $this->database->query($sql);
	}

	public function getUserNotificationsFeeds($user_id, $last_id){
		
		$filter = "";
		if($last_id)
			$filter = "AND un.`id` > '$last_id'";

		$sql = "SELECT 
					un.`id`, 
					un.`user_id`, 
					un.`notification_id`, 
					un.`content`, 
					DATE_FORMAT(un.`created_on`, '%D %M %Y %r') as 'created_on',
					n.`activity`
				FROM `user_notifications` un 
					JOIN `notifications` n ON n.`id` = un.`notification_id`
				WHERE un.`user_id` = '$user_id' $filter AND n.`activity` != 'FRIEND_RATED_FRIEND' ORDER BY un.`id` DESC";

		return $this->database->query($sql);
	}

	public function getUserNotificationsF2FFeeds($user_id, $last_id){
		
		$filter = "";
		if($last_id)
			$filter = "AND un.`id` > '$last_id'";

		if(!$user_id)
			return array();

		$user_id = implode(",", $user_id);

		$sql = "SELECT 
					un.`id`, 
					un.`user_id`, 
					un.`notification_id`, 
					un.`content`, 
					DATE_FORMAT(un.`created_on`, '%D %M %Y %r') as 'created_on',
					n.`activity`
				FROM `user_notifications` un 
					JOIN `notifications` n ON n.`id` = un.`notification_id`
				WHERE un.`user_id` IN ($user_id) $filter AND n.`activity` = 'FRIEND_RATED_FRIEND' ORDER BY un.`id` DESC";

		return $this->database->query($sql);
	}

	public function getUserNotificationsPicChangedFeeds($user_id, $last_id){
		
		$filter = "";
		if($last_id)
			$filter = "AND un.`id` > '$last_id'";

		if(!$user_id)
			return array();

		$user_id = implode(",", $user_id);

		$sql = "SELECT 
					un.`id`, 
					un.`user_id`, 
					un.`notification_id`, 
					un.`content`, 
					DATE_FORMAT(un.`created_on`, '%D %M %Y %r') as 'created_on',
					n.`activity`
				FROM `user_notifications` un 
					JOIN `notifications` n ON n.`id` = un.`notification_id`
				WHERE un.`user_id` IN ($user_id) $filter AND n.`activity` = 'FRIEND_CHANGED_PIC' ORDER BY un.`id` DESC";

		return $this->database->query($sql);
	}

	public function getActiveFriends($user_id){

		$sql = "SELECT * FROM `user_relationship`
					WHERE (`user_one_id` = $user_id OR `user_two_id` = $user_id) AND `status` = 'ACCEPTED'";
					
		return $this->database->query($sql);
	}

	public function updateNotificationAlerts($ids){

		if(!$ids)
			return;

		$sql = "UPDATE `user_notifications` SET `is_read` = 1
						WHERE `id` IN ($ids)";

		$this->database->update($sql);
	}

	public function getUnreadNotificationAlerts($user_id){

		$sql = "SELECT count(*) FROM `user_notifications` WHERE `is_read` = 0 AND `user_id` = $user_id AND `notification_id` NOT IN (6,7)";

		return $this->database->query_scalar($sql);
	}
}
?>