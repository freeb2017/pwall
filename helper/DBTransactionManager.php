<?php

/**
 * So that transaction can be managed from one place.
 * 
 * Instantiating the transaction manager each time would help 
 * in maintaining the transaction health.
 * 
 * Should not be initialized in constructor
 * @author nikul
 */
class TransactionManager{
	
	private $db;
	private $is_commit_enabled = true;
	
	public function __construct(){

		$this->db = new Dbase( 'pwall' );
	}
	
	/**
	 * starts the transaction for you
	 * @param Dbase $db
	 */
	public function beginTransaction( )
	{
		if( $this->db->isTransactionExists() ){
	
			$this->is_commit_enabled = false;
			return;
		}
	
		$this->db->update("SET autocommit = 0;");
		$this->db->update("START TRANSACTION;");
	}
	
	public function commitTransaction()
	{
	
		if( !$this->is_commit_enabled )
			return;
	
		$this->db->update("COMMIT");
		$this->db->update("SET autocommit = 1;");
	}
	
	public function rollbackTransaction()
	{
	
		if( !$this->is_commit_enabled )
			return;
	
		$this->db->update("ROLLBACK");
		$this->db->update("SET autocommit = 1;");
	}
	
}
?>