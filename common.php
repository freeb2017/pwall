<?php

ini_set('memory_limit', '500M');
ini_set('default_charset', 'utf-8');
set_include_path( get_include_path() . PATH_SEPARATOR . $_SERVER['DOCUMENT_ROOT'] );

// Loading few required files through out application
require_once("helper/Dbase.php");
require_once("helper/Util.php");
require_once("helper/DateUtil.php");
require_once("helper/Auth.php");

function custom_error_handler($errno, $errstr, $errfile, $errline) {
	global $logger;
	//print "here";
	//$bt = print_r(debug_backtrace(), true);
	$error_str = "$errfile:$errline -- ERROR $errno ==> $errstr\n";
	switch ($errno) {
		case E_ERROR:
		case E_CORE_ERROR:
		case E_COMPILE_ERROR:
		case E_RECOVERABLE_ERROR:
			error_log($error_str);
			break;			
		default:
			break;
	}
	return false;
}
$old_error_handler = set_error_handler("custom_error_handler");

function praisewallautoload($classname) {
	global $logger;

	$file = "";

	if (($match = preg_match("/([\w]+)AjaxService/", trim($classname), $args)) != false) {
		
		$file = "ajax/$classname.php";
	}
	else if (($match = preg_match("/([\w]+)Logger/", trim($classname), $args)) != false) {
		$file = "lib/PwallLogger.php";
	}
	else if (($match = preg_match("/([\w]+)Controller/", trim($classname), $args)) != false) {
		$file = "business_controller/$classname.php";	
	}
	else if (($match = preg_match("/([\w]+)Exception/", trim($classname), $args)) != false) {
		$file = "exceptions/ExceptionHandler.php";
	}
	else if (($match = preg_match("/([\w]+)Router/", trim($classname), $args)) != false) {
		$file = "routers/$classname.php";
	}
    else if(($match = preg_match("/[\w]+ModelExtension/", trim($classname), $args)) != false){
        $m = trim($args[1]);
        $file = "model_extension/class.$classname.php";
    }
    else if(($match = preg_match("/([\w]+)Model/", trim($classname), $args)) != false) {
        $m = trim($args[1]);
        $file = "base_model/class.$classname.php";
    }
	else {
		$file = "helper/$classname.php";
	}

	try{
        autoload_include_file($file);
	}
	catch(Exception $ex) {
		$logger->error($ex->getMessage());
		autoload_include_file($file);
		die();
	}
}

function autoload_include_file($file) {
	@include_once $file;
}

spl_autoload_register ('praisewallautoload');

ini_set('display_errors', 'Off');
?>
