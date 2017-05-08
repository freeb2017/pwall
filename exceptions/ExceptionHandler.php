<?php

class customException extends Exception{

	public function __toString() {

			return
				"EXCEPTION '".__CLASS__ ."' with message '".$this->getMessage().
				"' in ".$this->getFile().":".$this->getLine().
				"\nStack trace:\n".$this->getTraceAsString();
	}

}

class MySqlException extends customException {}

class SqlConstraintException extends MySqlException {}

class InvalidInputException extends Exception {}

class UserNotFoundException extends Exception {}

?>
