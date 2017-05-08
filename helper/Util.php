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

    public static function redirect( $module, $action ){
		global $prefix;
		header("Location: $prefix/$module/$action" );
	}
}
?>