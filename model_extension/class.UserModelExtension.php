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

	public function getSkillsAsOptions() {

		$sql = 'SELECT `name`,`id` FROM `qualities` 
					WHERE `is_active` = 1';
		return $this->database->query_hash($sql, "name", "id");
	}

	public function manageSkills($user_id, $skills) {
		
		if(empty($skills))
			return false;

		$skills_str = implode(",", $skills);

		$sql = "UPDATE `user_qualities` SET `is_active` = 0
					WHERE `quality_id` NOT IN ($skills_str)
						AND `user_id` = $user_id
						AND `is_active` = 1";

		$this->database->update( $sql );

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
					WHERE `user_id` = '$user_id'
						AND `is_active` = 1";

		return $this->database->query( $sql );
	}

	public function getSkillIdsByUser($user_id) {
		
		$sql = "SELECT quality_id FROM `user_qualities`
					WHERE `user_id` = '$user_id'
						AND `is_active` = 1";

		return $this->database->query( $sql );
	}
}
?>
