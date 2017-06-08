<?php 
include_once 'ui/page/base/BasePage.php';

/**
 * This is the change password page
 */
class ChangePasswordPage extends BasePage{

	public function __construct(){
		parent::__construct();
		$this->includeRequiredScripts('
			<script src="/js/profile.js"></script>
		');
	}
	
	public function loadWidgets(){
		
		include_once 'ui/widget/user/ChangePasswordWidget.php';
		$change = new ChangePasswordWidget();
		$this->callWidget($change);
	}
}
?>