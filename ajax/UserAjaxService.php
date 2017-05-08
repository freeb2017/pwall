<?php
/**
 * User ajax service support
 * 
 * @author nikul
 */
class UserAjaxService extends BaseAjaxService{
	
	public function __construct( $type, $params = null ){
		
		parent::__construct( $type, $params );
	}
	
	public function process(){
		
		$this->logger->debug( 'Checking For Type : ' . $this->type );
		
		switch ( $this->type ){
			
			case 'get_details' :
				
				$this->logger->debug( 'Fetching Actions For :' .print_r($this->params,true) );
				
				$this->getDetails( $this->params );
				
				break;
		}
	}

	/**
	 * @TODO Implementation pending
	 */
	private function getDetails($params) {

	}
}