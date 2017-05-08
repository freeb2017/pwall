<?php
/**
 * @author Nikul
 * Represents a database connection objects
 */
class DbConnection{

    protected $connection = null;

    protected $host = 'localhost';

    protected $port = 3306;

    //dbname to which we are connected with this handle
    protected $dbname = 'pwall';

    public function __construct($dbname, $h='localhost', $user='root', $password='root', $p=3306){

        $this->dbname = $dbname;
        $this->host = $h;
        $this->port = $p;
		
        $conn = new mysqli($this->host, $user, $password, $dbname);

        $this->connection = $conn;
   }

    /**
     * @return mysqli
     */
    public function getConnection(){ return $this->connection; }

    public function getDbname(){ return $this->dbname; }

    public function getHost() { return $this->host; }

    public function getPort() { return $this->port; }

    public function isAlive(){
        if($this->connection){
            return $this->connection->ping();
        }else{
            return null;
        }
    }

    public function kill(){
        if(!mysqli_kill($this->connection, mysqli_thread_id($this->connection)))
        {
            return false;
        }
        return true;
    }
}
?>