<?php 
include_once 'ui/widget/base/BaseWidget.php';

abstract class SingleStepWidget extends BaseWidget{
	
	public function __construct(){
		
		parent::__construct('SINGLE_STEP');
	}

	abstract public function init();

	abstract public function loadData();
	
	abstract public function execute();
	
	public function process(){

		$executed = $this->getWidgetExecutedStatus();
		if( $executed ) return;
			
		$this->setWidgetExecutedStatus( true );
		
		$this->loadData();
		
		if(isset($_POST) && count($_POST) > 0) {
			$this->execute();
			$this->loadData();
		}
	}
}
?>