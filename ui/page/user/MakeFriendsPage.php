<?php 
include_once 'ui/page/base/BasePage.php';

/**
 * This is the Friends page
 */
class MakeFriendsPage extends BasePage{

	public function __construct(){
		parent::__construct();
		$this->includeRequiredScripts('
			<script src="/js/friend.js"></script>
		');
	}
	
	public function loadWidgets(){
		
		include_once 'ui/widget/user/MakeFriendsWidget.php';
		$friends = new MakeFriendsWidget();
		$this->callWidget($friends);
	}
}

?>