<?php

/**
 * The Web Router Handles the follwoing logics :
 * loads the Page
 */
class WebRouter extends BaseRouter{

	private $ret;

	public function __construct( $urlParser ){
		parent::__construct( $urlParser );
	}

	/**
	 * resolves the namespace.
	 * formulates the path
	 * and includes the page file where it is lying
	 */
	private function resolveNameSpace(){

		$page_path = 'ui/page/';

		$name_space = explode( '::', $this->name_space );

		if( !is_array( $name_space ) )
			$name_space = array( $name_space );

		$name_space = implode( '/', $name_space );
		$page_path .= $name_space . '/' . ucfirst( $this->page ) . 'Page.php';

		try{

			include_once $page_path;
		}catch( Exception $e ){

			include_once "404Page.html";
			$this->logger->debug( "PAGE NOT FOUND ".$e->getMessage() );
			die();
		}
	}

	/**
	 * renders the page by passing the handling to the page.
	 * @param unknown_type $page
	 */
	private function renderPage( $page ){

		$page->render();
	}

	/**
	 * Redirect To the proper page
	 */
	public function doRedirect( ){

		$this->resolveNameSpace();

		$page = ucfirst( $this->page ) . 'Page' ;
		$page = new $page();
		$page->process( );

		ob_get_clean();

		$this->renderPage( $page );
	}
}
?>