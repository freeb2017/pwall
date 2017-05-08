<?php
/**
 * This file sets up the environment so that all the files are accessible and autoload is enabled.
 */
set_time_limit(7200); #Never kill in between... 1 hour

ini_set('memory_limit', '500M');
ini_set('default_charset', 'utf-8');
ini_set('zend.enable_gc' , 0);

set_include_path( get_include_path() . PATH_SEPARATOR . $_SERVER['DOCUMENT_ROOT'] );

// Loading few required files through out application
require_once("helper/Dbase.php");
require_once("helper/Util.php");
require_once("helper/DateUtil.php");
require_once("helper/Auth.php");

//////////////// CONFIGURATION VALUES ////////////////////
define("CONF_USERS_IS_EMAIL_REQUIRED", 'CONF_USERS_IS_EMAIL_REQUIRED');

//Global Error Codes
define('ERR_RESPONSE_SUCCESS', 1000);
define('ERR_RESPONSE_FAILURE', -1000);
define('ERR_RESPONSE_BAD_REQUEST_STRUCTURE', -2000);
define('ERR_RESPONSE_INVALID_CREDENTIALS', -3000);
define('ERR_RESPONSE_AUTENTICATION_REQUIRED', -7000);

//Global error messages
$GLOBALS["error_messages"] = array (
	ERR_RESPONSE_SUCCESS => 'Operation Successful',
	ERR_RESPONSE_FAILURE => 'Operation Unsuccessful',
	ERR_RESPONSE_BAD_REQUEST_STRUCTURE => 'Malformed request received. Unable to parse',
	ERR_RESPONSE_INVALID_CREDENTIALS => 'Wrong username or password',
	ERR_RESPONSE_AUTENTICATION_REQUIRED => 'Authentication Required'
);

//Global error keys
$GLOBALS["error_keys"] = array (
	ERR_RESPONSE_SUCCESS => 'ERR_RESPONSE_SUCCESS',
	ERR_RESPONSE_FAILURE => 'ERR_RESPONSE_FAILURE',
	ERR_RESPONSE_BAD_REQUEST_STRUCTURE => 'ERR_RESPONSE_BAD_REQUEST_STRUCTURE',
	ERR_RESPONSE_INVALID_CREDENTIALS => 'ERR_RESPONSE_INVALID_CREDENTIALS',
	ERR_RESPONSE_AUTENTICATION_REQUIRED => 'ERR_RESPONSE_AUTENTICATION_REQUIRED'
);

function getResponseErrorMessage($err_code) {
	global $error_messages;
	return $error_messages[$err_code];
}

function getResponseErrorKey($err_code) {
	global $error_keys;
	return $error_keys[$err_code];
}

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
	else if ( ( ($match = preg_match("/([\w]+)Model$/", trim($classname), $args)) != false ) 
				) {
		
		$legacy_model = array('basemodel', 'storemodel');
		if( in_array( strtolower( $classname ), $legacy_model ) ){

			$m = strtolower(trim($args[1]));
			$file = "model/$m.php";
		}else{
			
			$m = trim($args[1]);
			$file = "base_model/class.$m.php";
		}
	}
	else if (($match = preg_match("/([\w]+)Module/", trim($classname), $args)) != false) {
		
		$m = strtolower(trim($args[1]));
		$file = "module/$m.php";
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

// Display Errors off in case of Live Environment
// ini_set('display_errors', 'Off');
?>
