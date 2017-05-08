<?php 
include_once 'ui/widget/base/WidgetFactory.php';
include_once 'ui/page/base/BasePage.php';

/**
 * This is the Login page
 */
class LoginPage extends BasePage{

	public function __construct(){
		parent::__construct();
	}
	
	public function loadWidgets(){
		
		$login = WidgetFactory::getWidget( 'auth::LoginWidget' );
		$this->callWidget( $login );
	}
}

?>