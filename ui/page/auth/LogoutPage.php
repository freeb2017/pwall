<?php 
include_once 'ui/widget/base/WidgetFactory.php';
include_once 'ui/page/base/BasePage.php';

/**
 * This is the Logout page
 */
class LogoutPage extends BasePage{

	public function __construct(){
		parent::__construct();
	}
	
	public function loadWidgets(){
		
		$logout = WidgetFactory::getWidget( 'auth::LogoutWidget' );
		$this->callWidget( $logout );
	}
}

?>