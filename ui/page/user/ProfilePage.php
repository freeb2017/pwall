<?php 
include_once 'ui/page/base/BasePage.php';

/**
 * This is the User Profile page
 */
class ProfilePage extends BasePage{

	public function __construct(){
		parent::__construct();
		$this->includeRequiredScripts('
			<script src="/js/profile.js"></script>
		');
	}
	
	public function loadWidgets(){
		
		include_once 'ui/widget/user/ProfileWidget.php';
		$profile = new ProfileWidget();
		$this->callWidget($profile);
	}
}

?>