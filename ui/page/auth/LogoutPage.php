<?php 
include_once 'ui/page/base/BasePage.php';

/**
 * This is the Logout page
 */
class LogoutPage extends BasePage{

	public function __construct(){
		parent::__construct();
	}
	
	public function loadWidgets(){

		include_once 'ui/widget/auth/LogoutWidget.php';
		$logout = new LogoutWidget();
		$this->callWidget($logout);
	}
}

?>