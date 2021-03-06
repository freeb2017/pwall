<?php 
include_once 'ui/page/base/BasePage.php';

/**
 * This is the Public User Profile page
 */
class PublicProfilePage extends BasePage{

	public function __construct(){
		parent::__construct();
		$this->includeRequiredScripts('
			<script src="/js/profile.js"></script>
		');
	}
	
	public function loadWidgets(){
		
		include_once 'ui/widget/user/PublicProfileWidget.php';
		$profile = new PublicProfileWidget();
		$this->callWidget($profile);
	}
}

?>