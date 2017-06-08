<?php 
include_once 'ui/page/base/BasePage.php';

/**
 * This is the Login page
 */
class LoginPage extends BasePage{

	public function __construct(){
		parent::__construct();
	}
	
	public function loadWidgets(){
		
		include_once 'ui/widget/auth/LoginWidget.php';
		$login = new LoginWidget();
		$this->callWidget($login);
	}
}
?>