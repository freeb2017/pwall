<?php 

/**
 * The Base file
 * calls will be put in here. 
 * 
 * @author Nikul
 *
 */
abstract class BaseAjaxService {
	
	protected $type;
	protected $params;
	protected $data;
	public $logger;
	
	public function __construct( $type, $params ){
		
		global $data, $logger;
		
		$this->data = &$data;
		$this->type = $type;
		$this->params = $params;
		$this->logger = &$logger;
	}
	
	abstract public function process();
}
?>