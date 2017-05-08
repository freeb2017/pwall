<?php 
include_once 'ui/widget/base/WidgetFactory.php';
include_once 'ui/page/base/BasePage.php';

/**
 * This is the Login page
 */
class IndexPage extends BasePage{

	public function __construct(){
		parent::__construct();
	}
	
	public function loadWidgets(){
		
		$index = WidgetFactory::getWidget( 'user::IndexWidget' );
		$this->callWidget( $index );
	}
}

?>