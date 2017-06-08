<?php
 
include_once 'ui/widget/base/SingleStepWidget.php';

/**
 * Logout Widget
 */
class LogoutWidget extends SingleStepWidget{

	private $Auth;

	public function __construct(){
		parent::__construct();
	}
	
	public function init(){

		$_SESSION = array();
		
		session_destroy();

		// Start a new session
		session_start();

		// Generate a new session ID
		session_regenerate_id(true);

		Util::redirect("auth", "login");
	}

	public function loadData(){}
	
	public function execute(){}
	
	public function render(){}
}
?>