<?php

class Util {

	static $email_pattern ="/^[a-zA-Z0-9-_+.]+@[a-zA-Z0-9-_.]+\.[a-zA-Z]+$/i";
	static $username_pattern = "#^[a-z][\da-z_\.\-]{3,49}\$#";
	static $integer_pattern = "/^-{0,1}\d+$/";
	static $positive_integer_pattern = "/^\d+$/";
	static $name_pattern = "((?:[a-zA-Z][a-zA-Z]+))";
	static $optional_name_pattern = "#^((?:[a-z\sA-Z][a-z\sA-Z]+))?$#";

	public static function genUrl($module, $action) {
		global $prefix;
		return "$prefix/$module/$action";
	}

	public static function genUrlLink($link, $display) {
		return "<a href=\"$link\">$display</a>";
	}

	public static function beautify($str) {
		return trim(ucwords(str_replace('_', ' ', trim($str))));
	}

	/**
	 * Opposite of beautify. Eg: Hello World is converted to hello_world
	 */
	public static function uglify($str) {
		return trim(strtolower(str_replace(' ', '_', trim($str))));
	}

	public static function redirectByUrl( $url ){
		global $prefix;
		header("Location: $prefix/$url" );
	}	

    public static function checkEmailAddress($email){
		return preg_match(Util::$email_pattern,$email);
	}

	public static function getErrorList() {
    	return array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR);
    }

    public static function mysqlEscapeString($unescaped_string) {
        $escaped_string = mysql_escape_string($unescaped_string);
        return $escaped_string;
    }

    public static function redirect( $module, $action, $flash ){
		global $prefix;
		header("Location: $prefix/$module/$action?flash=".$flash );
	}

	public static function getDefaultValue($value, $default = 'Not Given'){
		return $value ? $value : $default;
	}

	public static function templateReplace($template, array $options, array $defaults = NULL) {
		if ($defaults == NULL) $defaults = array();

		foreach ($options as $k => $v) {

			if ($v == false) $v = $defaults[$k];
 			//echo "replacing $k with $v <br>";
			$template = str_ireplace('{{'.$k.'}}', $v, $template);
		}
		//if something didn't get replaced, just replace all the defaults once again
		foreach ($defaults as $k => $v) {
			$template = str_ireplace('{{'.$k.'}}', $v, $template);
		}
		return $template;
	}

	public static function getPageTitle(){

	  global $module, $action;
	  $pageTitle = "HOME";
	  if($module == 'user' && $action == 'index') {
	    $pageTitle = "Dashboard";
	  }else if($module == 'user' && $action == 'friends') {
	    $pageTitle = "Friends";
	  }else if($module == 'user' && $action == 'profile') {
	    $pageTitle = "Edit Profile";
	  }else if($module == 'user' && $action == 'publicProfile') {
	    $pageTitle = "User Profile";
	  }else if($module == 'user' && $action == 'makeFriends') {
	    $pageTitle = "List of People in PraiseWALL";
	  }else if($module == 'user' && $action == 'suggested') {
	    $pageTitle = "Suggested Qualities by My Friends";
	  }else if($module == 'admin' && $action == 'index') {
	    $pageTitle = "Dashboard";
	  }else if($module == 'user' && $action == 'changePassword') {
	    $pageTitle = "Change Password";
	  }
	  return $pageTitle;
	}

	public static function getPageSubTitle() {

	  global $module, $action;
	  $pageSubtitle = "";
	  if($module == 'user' && $action == 'index') {
	    $pageSubtitle = "Place to see your insights";
	  }else if($module == 'user' && $action == 'makeFriends') {
	    $pageSubtitle = "Expand your network";
	  }else if($module == 'user' && $action == 'friends') {
	    $pageSubtitle = "My network";
	  }else if($module == 'admin' && $action == 'index') {
	    $pageSubtitle = "Place to see your company analytics";
	  }
	  return $pageSubtitle;
	}
}
?>