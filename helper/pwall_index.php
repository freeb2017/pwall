<?php
	$logger->info("Session: ".print_r($_SESSION, true));
	if( isset( $_SESSION['parent_page_refersh_flash'] ) ){

		$flash_message = $_SESSION['parent_page_refersh_flash'];
		unset( $_SESSION['parent_page_refersh_flash'] );
	}

	$currentuser = $user;

	if($_SERVER['REQUEST_URI'] == '/')
		header('Location: /user/index');

	$data = array();

	if($module == "ajax")
		$router_class = new AjaxRouter( $urlParser );
	else
		$router_class = new WebRouter( $urlParser );

	$router_class->doRedirect();
	
	$data['flash_message']= $flash_message;
?>