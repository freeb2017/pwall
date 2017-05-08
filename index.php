<?php
/**
 * Start for all praise wall related activities. This file does the following:
 * - Find the URL using urlParser
 * - Call the relevant action in the relevant module
 * - Display the result in the required format
 */

ini_set("zend.enable_gc", 0);

ob_start();

// This is the default value set in php.ini
$old_error_level = error_reporting(E_ALL ^ E_NOTICE );

// Start of session
session_start();

# Find the prefix of the path
$prefix = substr($_SERVER['PHP_SELF'], 0, stripos($_SERVER['PHP_SELF'], 'index.php', 1) - 1);

// Handler to add few common helper files, autoloader, error codes and constants
require_once('common.php');

// Initialize the logger
$logger = new PwallLogger();
$logger->enabled = true;

// to show flash message for success and error msgs on topbar
$flash_message = "";
$request_type = 'WEB';

$url = isset( $_GET['url'] ) ? $_GET['url'] : "";

// To parse the params from the url
$urlParser = new UrlParser();
$nameSpace = $urlParser->getNameSpace();
$action = $page =  $urlParser->getPage();
$module = $urlParser->getModule();
$params = $urlParser->getParams();

// To initiate auth handler on start of application
$auth = Auth::getInstance();

//This is a hack to overcome session locking issue in concurrent ajax calls.
//check module is xaja | store health is the action do session write close
if($module == 'ajax')
	Auth::session_force_end();

$from = $urlParser->getFrom();
$flash_message = $urlParser->getFlashMessage();

//TODO : we have to remove all the style from here
if( ( $module == 'style' ) || ( $module == 'images' ) ||  ( $module == 'js' ) ){ echo "404 PAGE ERROR"; die(); };

/**
 * Add loggable user in auth...
 */
if ( $auth->isLoggedIn() ) {
	$currentuser = $user = $auth->user_data;
} else {
	// header('Location: /auth/login');
}

if($auth->canProceed()){
	include_once 'helper/pwall_index.php';
}

// Setting up headers for output stream to UI
$returnType = $urlParser->getReturnType();
if($returnType == 'json'){
	$logger->info("Displaying JSON: $w/$layout");
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
