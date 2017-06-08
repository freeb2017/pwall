<?php
/**
 * Start for all praise wall related activities. */

ob_start();

// This is the default value set in php.ini
$old_error_level = error_reporting(E_ALL ^ E_NOTICE );

// Start of session
session_start();

// Handler to add few common helper files, autoloader, error codes and constants
require_once('common.php');

// Initialize the logger
$logger = new PwallLogger();
$logger->enabled = true;

// to show flash message for success and error msgs on topbar
$flash_message = "";

$url = isset( $_GET['url'] ) ? $_GET['url'] : "";

// To parse the params from the url
$urlParser = new UrlParser();
$nameSpace = $urlParser->getNameSpace();
$action = $page =  $urlParser->getPage();
$module = $urlParser->getModule();
$params = $urlParser->getParams();

// To initiate auth handler on start of application
$auth = Auth::getInstance();

if($module == 'ajax')
	Auth::session_force_end();

$from = $urlParser->getFrom();
$flash_message = $urlParser->getFlashMessage();

if( ( $module == 'style' ) || ( $module == 'images' ) ||  ( $module == 'js' ) ){ echo "404 PAGE ERROR"; die(); };

/**
 * Add loggable user in auth...
 */
if ( $auth->isLoggedIn() ) {
	$currentuser = $user = $auth->user_data;
}

if($auth->canProceed()){
	include_once 'helper/pwall_index.php';
}

// Setting up headers for output stream to UI
$returnType = $urlParser->getReturnType();
if($module == 'ajax'){
	$logger->info("Displaying JSON: $w/$layout");

	if($data["status"]){
		header('HTTP/1.1 200 Success');
	}else{
		header('HTTP/1.1 500 Internal Server Error');
	}

	Header("Content-type: application/x-javascript");
	foreach($data as $key=>$value)
		$data[$key] = is_null($data[$key])?array():$data[$key];
	echo json_encode($data);
}else{
    @header("Content-type: text/html; charset=utf-8");       
}

$logger->info("DONE");

ob_flush();
?>
