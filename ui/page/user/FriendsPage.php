<?php 
include_once 'ui/widget/base/WidgetFactory.php';
include_once 'ui/page/base/BasePage.php';

/**
 * This is the Friends page
 */
class FriendsPage extends BasePage{

	public function __construct(){
		parent::__construct();
		$this->includeRequiredScripts('
			<script src="/plugins/datepicker/bootstrap-datepicker.js"></script>
			<script src="/plugins/select2/select2.full.min.js"></script>
			<script src="/js/profile.js"></script>
		');
	}
	
	public function loadWidgets(){
		
		$friends = WidgetFactory::getWidget( 'user::FriendsWidget' );
		$this->callWidget( $friends );
	}
}

?>