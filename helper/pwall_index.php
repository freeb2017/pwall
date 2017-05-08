<?php
	$logger->info("Session: ".print_r($_SESSION, true));
	if( isset( $_SESSION['parent_page_refersh_flash'] ) ){

		$flash_message = $_SESSION['parent_page_refersh_flash'];
		unset( $_SESSION['parent_page_refersh_flash'] );
	}

	if($_SERVER['REQUEST_URI'] == '/')
		header('Location: /user/index');

	$currentuser = $user;
	$router_class = new WebRouter( $urlParser );
	$router_class->doRedirect();	

	$data = array();
	$data['flash_message']= $flash_message;
?>