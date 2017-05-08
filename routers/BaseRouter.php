<?php 

class BaseRouter{
	
	protected $url;
	protected $name_space;
	protected $page;
	protected $params;
	
	protected $currentuser;
	
	public function __construct( $urlParser ){
		
		global $currentuser;
		
		$this->currentuser = $currentuser;

		$this->parseUrl( $urlParser );
	}

	/**
	 * Parse the url.
	 * 
	 * Call the controller service
	 */
	private function parseUrl( $urlParser ){
		
		$this->name_space = $urlParser->getNameSpace();
		$this->page = $urlParser->getPage();
		$this->params = $urlParser->getParams();
	}
}
?>