<?php 

/**
 * The Base Parent Class for Ajax Serivce
 * calls will be put in here. 
 * 
 * @author Nikul
 *
 */
abstract class BaseAjaxService {
	
	protected $type;
	protected $data;
	protected $logger;
	
	public function __construct($type){
		
		global $data, $logger;
		
		$this->data = &$data;
		$this->type = $type;
		$this->logger = &$logger;
	}
	
	abstract public function process();
}
?>