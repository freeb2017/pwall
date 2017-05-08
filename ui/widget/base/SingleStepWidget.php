<?php 
include_once 'ui/widget/base/BaseWidget.php';

/**
 * Single Step widget is a generic widget 
 * which renders the functions accordingly!!!
 * 
 */

abstract class SingleStepWidget extends BaseWidget{
	
	public function __construct( $widget_name = '' ){
		
		parent::__construct('SINGLE_STEP');
		$this->setName( $widget_name );
	}

	abstract public function init();

	abstract public function loadData();
	
	abstract public function execute();
	
	/**
	 * process all the form widgets
	 */
	public function process(){

		//Check executed or not
		$executed = $this->getWidgetExecutedStatus();
		if( $executed ) return;
			
		//STEP 0 : mark executed status
		$this->setWidgetExecutedStatus( true );
		
		$this->loadData();
		
		if(isset($_POST) && count($_POST) > 0) {
			//STEP 1 : Loads Data
			$this->execute();
			$this->loadData();
		}
	}
}
?>