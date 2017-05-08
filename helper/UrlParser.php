<?php

/**
 * @author nikul
 */
class UrlParser {

	private $m_url;
	private $m_container;
	private $m_flash;
	private $m_from;
	
	private $page;
	private $m_nameSpace;
	private $m_module = 'user';
	private $m_action = 'index';
	
	private $m_params = array();
	
	private $m_return_type = "html";
	private $m_call_type = 'web';
	private $explode_param = '/';	
	
	public function UrlParser( $url = false ){
		
		if( !$url ){
			$this->m_container = $_GET;
			$this->m_container['url'] = 
				explode('?', substr( $_SERVER['REQUEST_URI'], 1 ));
			$this->m_container['url'] =
				$this->m_container['url'][0];
		}
		else
			$this->m_container = $url;
		
		$this->setFlash();
		$this->setFrom();
		
		$this->setUrl();
		$this->parseUrl();
		$this->setReturnType();
	}	

	function getUrl(){
		
		return $this->m_url;
	}
	
	function getNameSpace(){
		
		if( !$this->m_nameSpace )
			return 'user';

		return $this->m_nameSpace;
	}
		
	function getPage(){
		
		if( !$this->page )
			return 'index';
			
		return $this->page;
	}

	function getFlashMessage() {

		return $this->m_flash;
	}
	
	function getFrom(){
		
		return $this->m_from;
	}	
	
	function getParams() {
		
		return $this->m_params;
	}
	
	function getReturnType() {
		
		return strtolower( $this->m_return_type );
	}
	
	function getModule(){
		return $this->m_module;
	}	

	function getAction(){
		return $this->m_action;
	}
	
	private function setUrl(){
		
		$this->m_url = isset( $this->m_container['url'] ) ? $this->m_container['url'] : null;
	}
	
	private function setReturnType(){
		
		if( isset( $_GET['return_type'] ) )
			$this->m_return_type = strtolower( $this->m_container['return_type'] );
	}
	
	private function setNameSpace( $count, $path ){
		
		$name_space_path = $this->getNameSpacePath( $count, $path );
		$this->m_nameSpace = implode( $this->explode_param, $name_space_path );
	}

	private function setPage( $count, $path ){
		
		$page_index = $this->getPageIndex( $count );

		$action = $path[ $page_index ];
		
		$this->page = $action;
	}
	
	private function setFlash(){
		
		$this->m_flash = strip_tags($this->m_container['flash']);
	}
	
	private function setFrom(){
		
		$this->m_from = $this->m_container['from'];	
	}
	
	private function setParams(){
		
		unset( $this->m_container['url'] );
		unset( $this->m_container['from'] );
		unset( $this->m_container['flash'] );
		$this->m_params = $this->m_container;
	}
	
	private function getPageIndex( $count ){
		
		return $count - 1;
	}
	
	private function getNameSpacePath( $count, $path ){
		
		$page_index = $this->getPageIndex( $count );
		
		unset( $path[ $page_index ]);
		
		return $path;
	}
	
	private function parseUrl(){
		
		$path = explode( "/", $this->m_url );
		$depth = count( $path );

		$module_code = ( $path[0] != '' ) ?  $path[0] : 'user';
		$this->m_module = $module_code;

		$this->setPage( $depth, $path );
		$this->setNameSpace( $depth, $path );
		$this->setParams();

		// For ajax call parsing
		if($action && $this->m_module == 'ajax') {			
			$pos_of_dot = strpos( $action, "." );
			if ( $pos_of_dot > 0 ){
			
				$this->m_return_type = substr($action, $pos_of_dot+1);
				$action = substr($action, 0, $pos_of_dot);
			}
			$this->m_action = $action;
			$this->m_call_type = 'ajax';
		}

		if($this->m_module == 'auth') {
			$this->m_call_type = 'auth';
		}
	}

	public static function isAjaxRequest(){
	
    	if( strtolower( self::$m_call_type ) == 'ajax' )
    		return true;
    		
        return false;
    }
    
    public static function isAuthRequests(){
    	
    	if( strtolower( self::$m_call_type ) == 'auth' )
    		return true;
    	
    	return false;
    }
}
	
?>
