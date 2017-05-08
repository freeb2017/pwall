<?php

/**
 *
 * BaseController has all the global objects defined in here.
 *
 * @author nikul
 *
 */
class BaseController{

	protected $logger;
	protected $currentuser;
	protected $logged_in_user = -1;
	protected $user_id = -1;

	protected $Auth;

	private $error_keys;
	private $error_responses;
	public function __construct( &$error_responses = false, &$error_keys = false ){

		global $currentuser, $logger;

		$this->logger = &$logger;
		
		if( $currentuser ){

			$this->currentuser = &$currentuser;
			$this->user_id = $currentuser->getUserId();

			$this->Auth = Auth::getInstance();
			$this->logged_in_user = $this->Auth->getLoggedInUser();

			if( $error_responses && $error_keys ){

				$this->error_responses = $error_responses;
				$this->error_keys = $error_keys;
			}
		}
	}

	/**
	 * Returns the message attached to the code
	 * @param unknown_type $err_code
	 */
	public function getResponseErrorMessage( $err_code ) {

		if ($err_code > 0)
			return "SUCCESS";

		return $this->error_responses[$err_code];
	}

	/**
	 * Returns the key for the error
	 *
	 * @param unknown_type $err_code
	 */
	public function getResponseErrorKey( $err_code ){

		if ($err_code > 0)
			$err_code = SUCCESS;

		return $this->error_keys[$err_code];
	}
}
?>
