<?php 
include_once 'ui/page/base/BasePage.php';

/**
 * This is the Login page
 */
class IndexPage extends BasePage{

	public function __construct(){
		parent::__construct();
	}
	
	public function loadWidgets(){
		
		include_once 'ui/widget/user/IndexWidget.php';
		$index = new IndexWidget();
		$this->callWidget($index);
	}
}

?>