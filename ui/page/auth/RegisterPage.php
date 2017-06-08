<?php 
include_once 'ui/page/base/BasePage.php';

/**
 * This is the Login page
 */
class RegisterPage extends BasePage{

	public function __construct(){
		parent::__construct();
		$this->includeRequiredScripts('
			<link rel="stylesheet" href="/plugins/datepicker/datepicker3.css">
			<script src="/plugins/datepicker/bootstrap-datepicker.js"></script>
			<script src="/js/register.js"></script>
		');
	}
	
	public function loadWidgets(){
		
		include_once 'ui/widget/auth/RegisterWidget.php';
		$register = new RegisterWidget();
		$this->callWidget($register);
	}
}

?>