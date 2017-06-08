<?php
include_once 'base_model/class.UserModel.php';
/**
 *
 * @author nikul
 *
 *This class extends the User Model.
 * 
 * In this class user level extra db queries will be included
 */
class UserModelExtension extends UserModel{
	
	/**
	 * CONSTRUCTOR
	 */
	public function __construct( ){

		parent::UserModel();
	}

	public function getCountriesAsOptions() {

		$sql = 'SELECT `name`,`id` FROM `countries`';
		return $this->database->query_hash($sql, "name", "id");
	}

	public function checkEmailExists($email, $user_id = -1) {

		$filter = '';
		if($user_id > 0)
			$filter = '`user_id` != "$user_id"';

		$sql = "SELECT * FROM `users`
					WHERE `email` = '$email'
						AND `is_active` = 1 $filter";

		return $this->database->query_scalar( $sql );
	}

	public function getUserByEmail($email) {

		$sql = "SELECT * FROM `users`
					WHERE `email` = '$email'
						AND `is_active` = 1";

		return $this->database->query_firstrow( $sql );
	}

	public function getSkillsAsOptions() {

		$sql = 'SELECT `name`,`id` FROM `qualities` 
					WHERE `is_active` = 1';
		return $this->database->query_hash($sql, "name", "id");
	}

	public function manageSkills($user_id, $skills, $no_update = false) {
		
		if(!$no_update){
			
			$skills_str = implode(",", $skills);

			$filter = '`quality_id` NOT IN ($skills_str) AND ';
			if($skills){
				$filter = "";
			}

			$sql = "UPDATE `user_qualities` SET `is_active` = 0
						WHERE $filter `user_id` = $user_id
							AND `is_active` = 1";

			$this->database->update( $sql );
		}

		$skill_sql = array();

		foreach ($skills as $skill_id) {
			array_push($skill_sql,"($user_id,$skill_id,1)");
		}

		$sql = "

			INSERT INTO user_qualities
			( 
				user_id,
				quality_id,
				is_active
			) 
			VALUES 
			".implode(",", $skill_sql)."
  			ON DUPLICATE KEY UPDATE is_active=VALUES(is_active);";
		
		$status = $this->database->update( $sql );
		 
		return $status;
	}

	public function getSkillsByUser($user_id) {
		
		$sql = "SELECT q.name as 'quality_name',q.id as 'quality_id', uq.id as 'user_quality_id'
					FROM `user_qualities` uq 
						JOIN `qualities` q ON q.`id` = uq.`quality_id`
					WHERE uq.`user_id` = '$user_id'
						AND uq.`is_active` = 1";

		return $this->database->query( $sql );
	}

	public function getSkillIdsByUser($user_id) {
		
		$sql = "SELECT quality_id FROM `user_qualities`
					WHERE `user_id` = '$user_id'
						AND `is_active` = 1";

		return $this->database->query( $sql );
	}

	public function getUserListDetails($user_id, $include = false){

		$filter = "";
		
		if(!$include)
			$filter = " u.`user_id`!='$user_id' AND ";	

		$sql = "SELECT 
					u.user_id,
					u.`email`,
					up.`username`,
					up.`gender`,
					DATE_FORMAT(up.`dob`, '%d %b %Y') as 'dob',
					c.`name` as 'location',
					up.`picture`,
					up.`phone`,
					up.`likes`,
					up.`dislikes`,
					up.`hobbies`,
					CASE WHEN GROUP_CONCAT(q.`name` SEPARATOR ',') IS NULL 
						THEN 'Not yet added' 
						ELSE GROUP_CONCAT(q.`name` SEPARATOR ',') 
					END as 'qualities'
				FROM `users` u 
					JOIN `user_profiles` up ON u.`user_id` = up.`user_id` AND u.`role` != 'ADMIN' AND up.`is_active` = 1
					JOIN `countries` c ON c.`id` = up.`country_id`
					LEFT OUTER JOIN `user_qualities` uq ON uq.`user_id` = u.`user_id` AND uq.`is_active` = 1
					LEFT OUTER JOIN `qualities` q ON q.`id` = uq.`quality_id` 
				WHERE $filter u.`is_active` = 1 GROUP by u.`user_id`";

		return $this->database->query($sql);
	}

	public function getFriendsList($user_id){

		$sql = "SELECT * FROM `user_relationship`
					WHERE (`user_one_id` = $user_id OR `user_two_id` = $user_id) AND is_active = 1";

		return $this->database->query($sql);
	}

	public function insertFriendRequest(
			$user_one_id, $user_two_id, $user_action_id
		){

		$user1 = $user_one_id;
		$user2 = $user_two_id;
		if($user_one_id > $user_two_id){
			$user1 = $user_two_id;
			$user2 = $user_one_id;
		}

		$dtime = DateUtil::getCurrentDateTime();

		$sql = "INSERT INTO `user_relationship` 
					(`user_one_id`, `user_two_id`, `status`, `action_user_id`,`last_modified_on`)
				VALUES 
					($user1, $user2, 'PENDING', $user_action_id, '$dtime')";

		return $this->database->insert($sql);
	}

	public function updateFriendRequest(
			$user_one_id, $user_two_id, $user_action_id, $status
		){

		$dtime = DateUtil::getCurrentDateTime();

		$sql = "UPDATE `user_relationship` 
					SET `status` = '$status', `action_user_id` = '$user_action_id', `last_modified_on` = '$dtime'
				WHERE 
					`user_one_id` = $user_one_id 
						AND 
					`user_two_id` = $user_two_id";

		return $this->database->update($sql);
	}

	public function checkFriendshipStatus($user_one_id, $user_two_id, $status = false){

		$filter = "";
		if($status)
			$filter = "AND `status`='$status'";

		$user1 = $user_one_id;
		$user2 = $user_two_id;
		if($user_one_id > $user_two_id){
			$user1 = $user_two_id;
			$user2 = $user_one_id;
		}

		$sql = "SELECT * FROM `user_relationship`
					WHERE 
				`user_one_id` = $user1
					AND 
				`user_two_id` = $user2 $filter";

		return $this->database->query_firstrow($sql);		
	}

	public function getActiveFriendsCount($user_id){

		$sql = "SELECT count(*) FROM `user_relationship`
					WHERE (`user_one_id` = $user_id OR `user_two_id` = $user_id) AND `status` = 'ACCEPTED'";

		return $this->database->query_scalar($sql);
	}

	public function getOverallRatingsBySkill($user_id){
		
		$sql = "SELECT 
					uq.user_id,
					q.name as 'quality_name',
					q.id as 'quality_id',
					count(uqr.rate_id) as 'count',
					CASE WHEN ROUND(AVG(DISTINCT(rate_id)),1) IS NULL THEN 0 ELSE ROUND(AVG(DISTINCT(rate_id)),1) END as 'rating'
				FROM `user_qualities` uq 
            		JOIN `qualities` q ON q.`id` = uq.`quality_id`
            		LEFT OUTER JOIN `user_quality_ratings` uqr ON uqr.user_id = uq.user_id AND uqr.`quality_id` = uq.`quality_id`
	  			WHERE uq.`user_id` = '$user_id' AND uq.`is_active` = 1 group by uq.user_id, uq.`quality_id`";

		return $this->database->query($sql);	  			
	}

	public function getRatingsByRatedBy($user_id, $rated_by){
		
		$sql = "SELECT 
					q.name AS 'quality_name', 
					q.id AS 'quality_id',
					uqr.rate_id AS 'rating',
					r.`name` as 'rate_label'
				FROM  `user_qualities` uq
					JOIN  `qualities` q ON q.`id` = uq.`quality_id` 
					JOIN  `user_quality_ratings` uqr ON uqr.user_id = uq.user_id
						AND uqr.`quality_id` = uq.`quality_id`
					JOIN `ratings` r ON r.id = uqr.`rate_id`
				WHERE uq.`user_id`= '$user_id'
					AND uq.`is_active` = 1
					AND uqr.`rated_by` = '$rated_by'";

		return $this->database->query($sql);
	}

	public function getRatingBySkill($user_id, $quality_id){
		
		$sql = "SELECT 
					uq.user_id,
					q.name as 'quality_name',
					q.id as 'quality_id',
					count(uqr.rate_id) as 'count'
				FROM `user_qualities` uq 
            		JOIN `qualities` q ON q.`id` = uq.`quality_id`
            		JOIN `user_quality_ratings` uqr ON uqr.user_id = uq.user_id AND uqr.`quality_id` = uq.`quality_id`  		
	  			WHERE uq.`user_id` = '$user_id' AND uq.`quality_id` = $quality_id AND uq.`is_active` = 1 AND uqr.`rate_id` != 0";

		return $this->database->query_firstrow($sql);
	}

	public function manageRatingPerSkill($user_id, $quality_id, $rated_by, $rate_id) {
		
		$sql = "SELECT * FROM `user_quality_ratings` 
	  				WHERE `user_id` = '$user_id' 
	  					AND `quality_id` = '$quality_id'
	  					AND `rated_by` = '$rated_by'";

		$result = $this->database->query($sql);

		if($result && ($rate_id == 0)){
			$sql = "DELETE FROM `user_quality_ratings`
	  					WHERE `user_id` = '$user_id'
	  						AND `quality_id` = '$quality_id'
	  						AND `rated_by` = '$rated_by'";

			$this->database->update($sql);

			return true;
		}

		$sql = "

			INSERT INTO user_quality_ratings
			( 
				user_id,
				quality_id,
				rated_by,
				rate_id,
				created_on
			) 
			VALUES 
			( 
				$user_id,
				$quality_id,
				$rated_by,
				$rate_id,
				NOW()
			)
  			ON DUPLICATE KEY UPDATE rate_id=VALUES(rate_id);";
		
		$status = $this->database->update( $sql );
		 
		return true;
	}

	public function getRatingsAsOptions() {

		$sql = 'SELECT `name`,`id` FROM `ratings`';
		return $this->database->query_hash($sql, "name", "id");
	}

	public function manageSuggestions($user_id, $quality_id, $suggested_by) {
		
		if(!$quality_id){

			$sql = "DELETE FROM `user_quality_suggestions`
  					WHERE `user_id` = '$user_id' AND `suggested_by` = '$suggested_by'";

  			return $this->database->update($sql);
		} else {

			$sql = "DELETE FROM `user_quality_suggestions`
  					WHERE `user_id` = '$user_id'
  						AND `quality_id` NOT IN ($quality_id)
  						AND `suggested_by` = '$suggested_by'";	

  			$this->database->update($sql);
		}

		$qualities = explode(",", $quality_id);

		$values = array();
		foreach ($qualities as $key) {
			array_push($values, "($user_id, $key, $suggested_by, 0)");
		}

		$sql = "
				INSERT INTO user_quality_suggestions
				( 
					user_id,
					quality_id,
					suggested_by,
					is_active
				) 
				VALUES ".implode(",", $values)
				." ON DUPLICATE KEY UPDATE quality_id=VALUES(quality_id);";
		
		$status = $this->database->update($sql);
		 
		return $status;
	}

	public function getSuggestedList($user_id, $suggested_by){

		$sql = "SELECT quality_id FROM `user_quality_suggestions`
  					WHERE `user_id` = '$user_id'
  						AND `suggested_by` = '$suggested_by' AND `is_active` != 1";	

  		return $this->database->query($sql);
	}

	public function getSuggestedSkillsByFriends($user_id){
		
		$sql = "SELECT 
					q.name as 'quality_name',
					GROUP_CONCAT(DISTINCT(up.`username`) SEPARATOR ', ') as 'friends',
					q.id as 'quality_id',
                    uqs.`is_active`
				FROM `user_quality_suggestions` uqs 
            		JOIN `qualities` q ON q.`id` = uqs.`quality_id`
                    JOIN `user_profiles` up ON up.`user_id` = uqs.suggested_by
				WHERE uqs.`user_id` = '$user_id' group by uqs.`quality_id` ORDER BY uqs.`last_modified_on` DESC";

		return $this->database->query($sql);	  			
	}

	public function updateSuggestions($user_id, $quality_id, $is_active) {
		
		if($is_active){
			$sql = "
				UPDATE user_quality_suggestions 
					SET is_active = '$is_active'
				WHERE user_id = '$user_id' 
					AND quality_id = '$quality_id'";
		}else{
			$sql = "
				DELETE FROM user_quality_suggestions WHERE user_id = '$user_id' 
						AND quality_id = '$quality_id'";
		}
		
		$status = $this->database->update($sql);
		 
		return $status;
	}

	public function filterSuggestedQualities($user_id){

		$sql = "SELECT quality_id 
					FROM `user_quality_suggestions`
  						WHERE `user_id` = '$user_id' 
  							AND `is_active` = 1";

  		return $this->database->query($sql);
	}

	public function deleteSuggestedQualities($user_id, $skills) {
		
		$filter = '';
		if($skills){
			$filter = " AND quality_id NOT IN ($skills)";
		}

		$sql = "
				DELETE FROM user_quality_suggestions 
					WHERE user_id = '$user_id' $filter";
		
		$status = $this->database->update($sql);
		 
		return $status;
	}

	public function getRateCountsByUser($user_id){
		
		$sql = "SELECT count(*) FROM `user_quality_ratings` WHERE `rated_by` = '$user_id'";

		return $this->database->query_scalar($sql);
	}

	public function getUsersPerCountry(){

		$sql = "SELECT count(DISTINCT(user_id)) as 'users', c.id, c.name , c.sortname, c.phonecode
					FROM `user_profiles` up 
						JOIN `countries` c ON c.id = up.country_id 
					group by up.country_id";

		return $this->database->query_hash($sql,"sortname","users");
	}

	public function getUsersPerDay(){

		$sql = "SELECT 
					DATE_FORMAT(T.`created_on`, '%D %M %Y') as 'created',
					COUNT(*) as 'users',
					DATE(T.`created_on`) as 'kdate'
				FROM users T JOIN (
   						SELECT MIN(created_on) as minDate, user_id
   						FROM users 
   						GROUP BY user_id
   					) T2 ON T.created_on = T2.minDate AND T.user_id = T2.user_id
				GROUP BY created ORDER BY kdate DESC";

		return $this->database->query($sql);		
	}

	public function getInvitedUsersPerDay($include_today = false){

		$filter = '';
		if ($include_today) {
			$date = DateUtil::getCurrentDate();
			$filter = " AND DATE(last_modified_on) = '$date'";
		}

		$sql = "SELECT 
					count(*) as 'users', 
					DATE_FORMAT(last_modified_on, '%D %M %Y') as 'date',
					DATE(last_modified_on) as 'kdate'
				FROM `user_relationship` 
					WHERE status = 'PENDING' $filter
				GROUP BY date ORDER BY kdate DESC";

		if ($include_today) {
			return $this->database->query_firstrow($sql);			
		}

		return $this->database->query($sql);
	}

	public function getAddedAsFriendsUsersPerDay($include_today = false){

		$filter = '';
		if ($include_today) {
			$date = DateUtil::getCurrentDate();
			$filter = " AND DATE(last_modified_on) = '$date'";
		}

		$sql = "SELECT 
					count(*) as 'users', 
					DATE_FORMAT(last_modified_on, '%D %M %Y') as 'date',
					DATE(last_modified_on) as 'kdate'
				FROM `user_relationship` 
					WHERE status = 'ACCEPTED' $filter
				GROUP BY date ORDER BY kdate DESC";

		if ($include_today) {
			return $this->database->query_firstrow($sql);			
		}

		return $this->database->query($sql);
	}

	public function getRejectedAsFriendsUsersPerDay($include_today = false){

		$filter = '';
		if ($include_today) {
			$date = DateUtil::getCurrentDate();
			$filter = " AND DATE(last_modified_on) = '$date'";
		}

		$sql = "SELECT 
					count(*) as 'users', 
					DATE_FORMAT(last_modified_on, '%D %M %Y') as 'date',
					DATE(last_modified_on) as 'kdate'
				FROM `user_relationship` 
					WHERE status = 'DECLINED' $filter
				GROUP BY date ORDER BY kdate DESC";

		if ($include_today) {
			return $this->database->query_firstrow($sql);			
		}

		return $this->database->query($sql);
	}

	public function getStatesAsOptions() {

		$sql = 'SELECT `name`, `id`, `country_id` FROM `states`';

		return $this->database->query($sql);
	}

	public function getCitiesAsOptions() {

		$sql = 'SELECT `name`, `id`, `state_id` FROM `cities`';

		return $this->database->query($sql);
	}

	public function changePassword($password, $user_id) {

		$sql = "
			UPDATE users 
				SET password = '$password'
			WHERE user_id = '$user_id'";
		
		$status = $this->database->update($sql);
		 
		return $status;
	}

	public function changePicture($picture, $user_id) {

		$picture = addslashes($picture);

		$sql = "
			UPDATE user_profiles 
				SET picture = '$picture'
			WHERE user_id = '$user_id'";
		
		$status = $this->database->update($sql);
		 
		return $status;
	}

	public function getPercentageToImproveUpon($user_id) {

		$sql = "SELECT 
					(SELECT count(DISTINCT(rated_by)) 
							FROM user_quality_ratings 
							WHERE user_id = $user_id
						) as 'users', 
					ROUND((count(DISTINCT(a1.rated_by)) * 100.0)/
						(SELECT count(DISTINCT(rated_by)) 
							FROM user_quality_ratings 
							WHERE user_id = $user_id
						),
					1) as 'percentage'
				FROM user_quality_ratings a1 
					WHERE a1.rate_id <= 2 AND a1.user_id = $user_id";

		$result = $this->database->query_firstrow($sql);
		 
		return $result;
	}

	public function getOverallResultByQualities($user_id) {

		$sql = "SELECT 
					count(uqr.rate_id) as 'count',
					CASE WHEN ROUND(AVG(DISTINCT(uqr.rate_id)),1) IS NULL THEN 0 ELSE ROUND(AVG(DISTINCT(uqr.rate_id)),1) END as 'rating'
				FROM `user_qualities` uq 
            		JOIN `qualities` q ON q.`id` = uq.`quality_id`
            		LEFT OUTER JOIN `user_quality_ratings` uqr 
            			ON uqr.user_id = uq.user_id AND uqr.`quality_id` = uq.`quality_id`
	  			WHERE uqr.`user_id` = '$user_id' AND uqr.`is_active` = 1 group by uqr.user_id";

		$result = $this->database->query_firstrow($sql);
		 
		return $result;
	}

	public function getTotalUsers(){

		$sql = "SELECT count(DISTINCT(user_id)) FROM `users`";

		return $this->database->query_scalar($sql);
	}
}
?>