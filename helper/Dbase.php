<?php

include_once('helper/DbConnection.php');

/**
 * Dbase Will allow to open connections to databases
 */
class Dbase {

    private $dbname;

    private $dbi;

    private $conn;

    /**
     * Retrieve the DB settings, connect
     * @param $dbname
     **/
    function __construct($dbname) {

        $this->dbname = $dbname;
        $this->dbi = new DbConnection($dbname);
    }

    /**
     * Get the number of affected rows by the last query
     * @return unknown_type
     */
    public function getAffectedRows() {
        return $this->conn->affected_rows;
    }

    // Actual function to fire queries to mysql and get the result
    private function execQuery($sql) {

        $this->conn = $this->dbi->getConnection();
        return $this->conn->query($sql);
    }


    /**
     * Private function that runs a particular query, maintains the timer, and checks for error
     * @param $sql
     * @return array ResultSet on success, false on error
     */
    private function runQuery($sql) {

        global $logger;

        $res = $this->execQuery($sql);

        $logger->sql("Executed SQL [$this->dbname] - $sql\nTime taken: ".$time_taken."\nNo. of rows affected: ".$this->conn->affected_rows);

        if ($res == false) {
            $errno = $this->conn->errno;
            $error = $this->conn->error;
            $logger->error("$errno - $error, host: $this->host");
            return false;
        }

        return $res;
    }

    /**
     * Execute an insert query, and return the last inserted ID
     * @param $sql Sql to execute
     * @return auto-increment value on success, false on failure
     */
    function insert($sql) {

        if ($this->runQuery($sql))
            return $this->conn->insert_id;

        return false;
    }

    /**
     * Runs an SQL query, and returns the success/failure status. This is mainly used for UPDATE/DELETE queries
     * @param $sql
     * @return bool true on success
     */
    function update($sql) {
        return $this->runQuery($sql);
    }


    private function getResultSetAsAssoc($res) {
        return $res->fetch_assoc();
    }


    private function getsAsSmallAssoc($row)
    {
        $keys = array_keys($row) ;
        foreach($keys as $key)
        {
            $row[$key] = (string)$row[$key] ;
            $row[$key] = substr($row[$key],0,100);
        }
        return $row ;
    }


    /**
     * Run the query, and return the results as an array of associative arrays (hashes)
     * @param $sql
     * @return array on success, and false on failure
     */
    function query($sql) {
        global $logger;

        $res = $this->runQuery($sql);
        if ($res) {
            $data = array();
            $small_data = array();
            $small_data_count = 0 ;
            while ($row = $this->getResultSetAsAssoc($res)) {
                array_push($data, $row);
                if($small_data_count < 1)
                {
                    $small_row = $this->getsAsSmallAssoc($row);
                    array_push($small_data, $small_row);
                    $small_data_count++ ;
                }
            }
            $logger->debug("Results: ".print_r($small_data, true));

            return $data;
        }
        return false;
    }

    /**
     * Takes out the value in the (1,1) element of the result. First row's first column
     * @param $sql
     * @return scalar value
     */
    function query_scalar($sql) {
        global $logger;

        $res = $this->query($sql);

        if ($res == false or count($res) < 1) {
            $logger->error("Not enough rows for scalar query in results");
            return NULL;
        }
        $first = $res[0];

        foreach ($first as $key => $val) {
            return $val;
        }
        $logger->error("Row doesn't have enough cols");
        return false;
    }


    /**
     *Returns the result as a hash if the query returns rows where one of the columns is unique
     * @param $sql
     * @param $key The column header which has to be the key for the hash
     * @param $values The column headers of the values to be returned for each key. If an array of headers is passed..  for each key, an array is stored
     * @return array|null
     */
    function query_hash($sql, $key, $values){

        global $logger;

        $hash_res = array();

        $res = $this->query($sql);

        if ($res == false or count($res) < 1) {
            $logger->error("Not enough rows for hash query in results");
            return NULL;
        }

        foreach($res as $row){
            $ret = "";
            if(count($values) > 1){
                //multiple values..  create an assoc array
                foreach($values as $val){
                    $ret[$val] = $row[$val];
                }
            }else
                //single item..  direct hash
                $ret = $row[$values];

            $hash_res[$row[$key]] = $ret;
        }

        return $hash_res;
    }

    /**
     * Returns the first result record
     * @param $sql
     * @return unknown_type
     */
    function query_firstrow($sql) {

        $ret = $this->query($sql);
        if ($ret) return $ret[0];
        return NULL;
    }

    /**
     * Return the first column of the results
     * @param $sql
     * @return unknown_type
     */
    function query_firstcolumn($sql) {

        $ret = $this->query($sql);
        if (!$ret) return NULL;
        $arr = array();
        # Ideally this should also get profiled... but its a different function so leaving it for now
        while ($row = $ret->fetch_array()) {
            array_push($arr, $row[0]);
        }
        return $arr;
    }

    public function realEscapeString($str) {
        $st = addslashes($str);
        return $st;
    }

    /**
     * checks if auto commit is on/off for particular transaction
     * @return Ambigous <scalar, NULL, boolean, unknown>
     */
    public function isTransactionExists(){

        $sql = "SELECT @@autocommit";
        return !$this->query_scalar($sql);
    }
}