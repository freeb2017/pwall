<?php 

include_once 'model_extension/class.UserModelExtension.php';
include_once 'business_controller/BaseController.php';
/** 
 * The Admin User Controller will handle the Analytics Dashboard for the company
 * @author nikul
 */
class AdminController extends BaseController{
	
	private $UserModel;
		
	public function __construct(){

		parent::__construct();

		$this->UserModel = new UserModelExtension();
	}

	public function getUsersPerCountry(){

		$result = $this->UserModel->getUsersPerCountry();

		return $result;
	}

	public function getUsersPerDay(){

		$result = $this->UserModel->getUsersPerDay();

		return $result;	
	}

	public function getInvitedUsersPerDay($include_today = false){

		$result = $this->UserModel->getInvitedUsersPerDay($include_today);

		if($include_today)
			$result = $result['users'] ? $result['users'] : 0;

		return $result;
	}

	public function getAddedAsFriendsUsersPerDay($include_today = false){

		$result = $this->UserModel->getAddedAsFriendsUsersPerDay($include_today);

		if($include_today)
			$result = $result['users'] ? $result['users'] : 0;
		
		return $result;	
	}

	public function getRejectedAsFriendsUsersPerDay($include_today = false){

		$result = $this->UserModel->getRejectedAsFriendsUsersPerDay($include_today);

		if($include_today)
			$result = $result['users'] ? $result['users'] : 0;

		return $result;
	}

	public function getTotalUsers(){

		$result = $this->UserModel->getTotalUsers();

		return $result;
	}
}
?>