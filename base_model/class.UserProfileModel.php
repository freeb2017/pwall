<?php

class UserProfileModel
{

	protected $id;   // KEY ATTR. WITH AUTOINCREMENT

	protected $user_id;
	protected $username;
	protected $gender;
	protected $dob;
	protected $country_id;
	protected $picture;
	protected $link;
	protected $phone;
	protected $likes;
	protected $dislikes;
	protected $hobbies;
	protected $is_active;
	protected $created_on;
	protected $last_modified_on;

	protected $database;

	protected $table = 'user_profiles';

	function UserProfileModel()
	{	
		global $logger;
		
		$this->logger = $logger;
		// $this->database = new Dbase( 'pwall' );
		$this->database = Dbase::getInstance();
	}

	function getId()
	{	
		return $this->id;
	}

	function getUserId()
	{	
		return $this->user_id;
	}

	function getUsername()
	{	
		return $this->username;
	}

	function getGender()
	{	
		return $this->gender;
	}

	function getDob()
	{	
		return $this->dob;
	}

	function getCountryId()
	{	
		return $this->country_id;
	}

	function getPicture()
	{	
		return $this->picture;
	}

	function getLink()
	{	
		return $this->link;
	}

	function getPhone()
	{	
		return $this->phone;
	}

	function getLikes()
	{	
		return $this->likes;
	}

	function getDislikes()
	{	
		return $this->dislikes;
	}

	function getHobbies()
	{	
		return $this->hobbies;
	}

	function getIsActive()
	{	
		return $this->is_active;
	}

	function getCreatedOn()
	{	
		return $this->created_on;
	}

	function getLastModifiedOn()
	{	
		return $this->last_modified_on;
	}

	function setId( $id )
	{
		$this->id =  $id;
	}

	function setUserId( $user_id )
	{
		$this->user_id =  $user_id;
	}

	function setUsername( $username )
	{
		$this->username =  $username;
	}

	function setGender( $gender )
	{
		$this->gender =  $gender;
	}

	function setDob( $dob )
	{
		$this->dob =  $dob;
	}

	function setCountryId( $country_id )
	{
		$this->country_id =  $country_id;
	}

	function setPicture( $picture )
	{
		$this->picture =  $picture;
	}

	function setLink( $link )
	{
		$this->link =  $link;
	}

	function setPhone( $phone )
	{
		$this->phone =  $phone;
	}

	function setLikes( $likes )
	{
		$this->likes =  $likes;
	}

	function setDislikes( $dislikes )
	{
		$this->dislikes =  $dislikes;
	}

	function setHobbies( $hobbies )
	{
		$this->hobbies =  $hobbies;
	}

	function setIsActive( $is_active )
	{
		$this->is_active =  $is_active;
	}

	function setCreatedOn( $created_on )
	{
		$this->created_on =  $created_on;
	}

	function setLastModifiedOn( $last_modified_on )
	{
		$this->last_modified_on =  $last_modified_on;
	}

	// **********************
	// SELECT METHOD / LOAD
	// **********************

	function load( $id )
	{

		$sql =  "SELECT * FROM user_profiles WHERE user_id = $id";
		$row =  $this->database->query_firstrow( $sql );
	
		$this->id = $row['id'];
		$this->user_id = $row['user_id'];
		$this->username = $row['username'];
		$this->gender = $row['gender'];
		$this->dob = $row['dob'];
		$this->country_id = $row['country_id'];
		$this->picture = $row['picture'];
		$this->link = $row['link'];
		$this->phone = $row['phone'];
		$this->likes = $row['likes'];
		$this->dislikes = $row['dislikes'];
		$this->hobbies = $row['hobbies'];
		$this->is_active = $row['is_active'];
		$this->created_on = $row['created_on'];
		$this->last_modified_on = $row['last_modified_on'];
	}

	function insert()
	{

		$this->id = ""; // clear key for autoincrement

		$sql =  "

			INSERT INTO user_profiles 
			( 
				user_id,
				username,
				gender,
				picture,
				dob,
				country_id,
				phone,
				likes,
				dislikes,
				hobbies,
				is_active,
				created_on,
				last_modified_on 
			) 
			VALUES 
			( 
				'$this->user_id',
				'$this->username',
				'$this->gender',
				'$this->picture',
				'$this->dob',
				'$this->country_id',
				'$this->phone',
				'$this->likes',
				'$this->dislikes',
				'$this->hobbies',
				'$this->is_active',
				'$this->created_on',
				'$this->last_modified_on' 
			)";
		
		return $this->id = $this->database->insert( $sql );

	}
	
	function insertWithId()
	{


		$sql =  "

			INSERT INTO user_profiles 
			( 
				id,
				user_id,
				username,
				picture,
				gender,
				dob,
				country_id,
				phone,
				likes,
				dislikes,
				hobbies,
				is_active,
				created_on,
				last_modified_on 

			) 

			VALUES 
			( 
				'$this->id',
				'$this->user_id',
				'$this->username',
				'$this->picture',
				'$this->gender',
				'$this->dob',
				'$this->country_id',
				'$this->phone',
				'$this->likes',
				'$this->dislikes',
				'$this->hobbies',
				'$this->is_active',
				'$this->created_on',
				'$this->last_modified_on' 

			)";
		
		return $this->database->update( $sql );
	}
	
	
	/**
	*
	*@param $id
	*/
	function update( $id )
	{

		$sql = " 
			UPDATE user_profiles 
			SET 
				username = '$this->username',
				gender = '$this->gender',
				dob = '$this->dob',
				country_id = '$this->country_id',
				phone = '$this->phone',
				likes = '$this->likes',
				dislikes = '$this->dislikes',
				hobbies = '$this->hobbies',
				is_active = '$this->is_active',
				created_on = '$this->created_on',
				last_modified_on = '$this->last_modified_on' 
			WHERE user_id = $id ";

		return $result = $this->database->update($sql);

	}

	/**
	*
	*Returns the hash array for the object
	*
	*/
	function getHash(){

		$hash = array();
 
		$hash['id'] = $this->id;
		$hash['user_id'] = $this->user_id;
		$hash['username'] = $this->username;
		$hash['gender'] = $this->gender;
		$hash['dob'] = $this->dob;
		$hash['country_id'] = $this->country_id;
		$hash['picture'] = $this->picture;
		$hash['link'] = $this->link;
		$hash['phone'] = $this->phone;
		$hash['likes'] = $this->likes;
		$hash['dislikes'] = $this->dislikes;
		$hash['hobbies'] = $this->hobbies;
		$hash['is_active'] = $this->is_active;
		$hash['created_on'] = $this->created_on;
		$hash['last_modified_on'] = $this->last_modified_on;

		return $hash;
	}
}
?>