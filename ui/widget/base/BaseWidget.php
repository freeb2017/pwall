<?php

abstract class BaseWidget{
	
	private $widget_executed_status;
	private $destroyed;
	protected $logger;
	
	public function __construct(){
		
		global $logger;
		$this->logger = &$logger;
	}
	
	protected function setWidgetExecutedStatus( $status ){
		
		$this->widget_executed_status = $status;
	}
	
	protected function getWidgetExecutedStatus( ){
		
		return $this->widget_executed_status;
	}	
	
	public function destroyMe(){
		
		$this->destroyed = true;
	}
	
	public function isDestroyed(){
		
		return $this->destroyed;	
	}
}
?>