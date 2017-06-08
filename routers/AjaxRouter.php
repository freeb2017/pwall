<?php

/**
 * The Ajax Router Handles the follwoing logics :
 * loads the Page
 */
class AjaxRouter extends BaseRouter{

	private $ret;

	public function __construct( $urlParser ){
		parent::__construct( $urlParser );
	}

	/**
	 * Redirect To the proper page
	 */
	public function doRedirect(){

		global $data, $logger, $action, $currentuser;

		ob_get_clean();

		include_once "ajax/PwallAjaxService.php";

		$logger->debug("Ajax:".$action.", user id:". $currentuser->getUserId());

		$page = new PwallAjaxService($action);
		$page->process();
	}
}
?>