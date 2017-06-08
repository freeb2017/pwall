<?php 
include_once 'ui/page/base/BasePage.php';

/**
 * This is the Friends page
 */
class FriendsPage extends BasePage{

	public function __construct(){
		parent::__construct();
		$this->includeRequiredScripts('
			<script src="/js/friend.js"></script>
		');
	}
	
	public function loadWidgets(){
		
		include_once 'ui/widget/user/FriendsWidget.php';
		$friends = new FriendsWidget();
		$this->callWidget($friends);
	}
}

?>