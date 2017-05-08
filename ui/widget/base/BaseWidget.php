<?php
/**
 * Parent Class of Widget
 * The abstract class contains the logic for
 * 1) Execution Condition
 * 2) Destructor for widget
 * 
 */
abstract class BaseWidget{
	
	private $message;
	private $widget_type;
	private $widget_executed_status;
	private $flash;
	private $widget_name;

	protected $title; //Title which be rendered on top of a table or a form
	protected $logger;
	
	public function __construct( $widget_type, $title = '' ){
		
		global $flash_message,$logger;
		$this->title = $title;
		$this->widget_type = $widget_type;
		$this->logger = &$logger;
		$this->flash = &$flash_message;
	}
	
	public function setName( $widget_name ){
		$this->widget_name = $widget_name;
	}
	
	public function getName(){
		
		return $this->widget_name;
	}
	
	/**
	 * The title of the widget that will be displayed
	 * above the widget
	 * 
	 * @param unknown_type $title
	 */
	public function setTitle( $title ){
		
		$this->title = $title;	
	}
	
	/**
	 * Sets the execution status of the widget 
	 * @param unknown_type $status
	 */
	protected function setWidgetExecutedStatus( $status ){
		
		$this->widget_executed_status = $status;
	}
	
	/**
	 * Returns if the widget is already executed.
	 */
	protected function getWidgetExecutedStatus( ){
		
		return $this->widget_executed_status;
	}	
	
	/**
	 * Returns the type of widget
	 * e.g; FORM/TABLE
	 */
	public function getWidgetType(){
		
		return $this->widget_type;
	}

	/**
	 * widget is destroyed and 
	 * is not considered for rendering
	 */
	public function destroyMe(){
		
		$this->destroyed = true;
	}
	
	/**
	 * Checks if destroyed the widget is not 
	 * considered for rendering
	 */
	public function isDestroyed(){
		
		return $this->destroyed;	
	}

	/**
	 * Returns Title Of the widget that is needed to be displayed
	 */
	public function getTitle(){
		return $this->title;
	}	
	
	protected function setFlashMessage( $msg ){
		$msg = strip_tags($msg);
		$this->flash = $msg;	
	}
	
	protected function getFlashMessage( ){
		
		return $this->flash;
	}
	
	public function setErrorMsg( $msg ){
		global $js, $data;
		
		
		$data['error'] = $msg;
		
		$this->flash = $msg;
		$js->setFlashMessageError();
	}
}
?>