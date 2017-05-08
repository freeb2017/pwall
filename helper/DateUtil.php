<?php

/**
 * Class for providing all date manipulation functions
 * in a single place.
 *
 * @author nikul
 */

class DateUtil{

    /**
     * Returns current date
     * @return string
     */
    public static function getCurrentDate(){
        return date('Y-m-d');
    }

    /**
     * @return Current datetime in Y-m-d H:i:s format
     */
    public static function getCurrentDateTime(){
        return date( 'Y-m-d H:m:s' );
    }
       
    /**
     * returns date with time as 00:00:00
     * @param $date
     * @return string
     */
    public static function getDateWithStartTime($date) 
    {
    	$timestamp = strtotime($date);
    	return date('Y-m-d', $timestamp)." 00:00:00";
    }

    /**
     * returns date with time as 23:59:59
     * @param $date
     */
    public static function getDateWithEndTime($date)
    {
    	$timestamp = strtotime($date);
    	return date('Y-m-d', $timestamp)." 23:59:59";
    }
    
    /**
     * returns date from given timestamp, returns false in case of failure
     * @param $php_timestamp
     * @return string - date format will be "j-M-Y"
     */
    public static function getDateAsString($php_timestamp) {
    	$d1 = $php_timestamp;
    	$d1 = (is_string($d1) ? strtotime($d1) : $d1);
    
    	if ($d1 == false) return false;
    	return date("j-M-Y", $d1);
    }

    public static function getDateAsDisplayString($php_timestamp) {
        $d1 = $php_timestamp;
        $d1 = (is_string($d1) ? strtotime($d1) : $d1);
    
        if ($d1 == false) return false;
        return date("d/m/Y", $d1);
    }
    
    /**
     * returns mysql date from given timestamp, return false in case of failure
     * @param  $php_timestamp
     * @return string - date format will be "Y-m-d"
     */
    public static function getMysqlDate($php_timestamp) {
    	if ($php_timestamp == false) $php_timestamp = time();
    	$d1 = $php_timestamp;
    	$d1 = (is_string($d1) ? strtotime($d1) : $d1);
    
    	if ($d1 == false) return false;
    	return date ("Y-m-d", $d1);
    }
    
    /**
     * returns mysql date with time from given timestamp, returns false in case of failure
     * @param $php_timestamp
     * @return string - date format will be "Y-m-d H:i:s"
     */
    public static function getMysqlDateTime($php_timestamp) {
    	if ($php_timestamp == false) $php_timestamp = time();
    	$d1 = $php_timestamp;
    	$d1 = (is_string($d1) ? strtotime($d1) : $d1);
    	if ($d1 == false) return false;
    	return date ("Y-m-d H:i:s", $d1);
    }
	
    public static function getStandardDate($date=false)
    {
        if($date===false)
            $date=time();
        
        $c1_date=strtotime($date);
        
        if($c1_date===false)
            $c1_date=$date;       
        
        return date('d M Y',$c1_date);          
    }
   
    public static function getStandardDatetime($datetime = false)
    {
        if($datetime === false)
            $datetime = time();
        
        $c1_datetime = strtotime($datetime);
        
        if($c1_datetime === false)
            $c1_datetime = $datetime;
        
        return date('d M Y H:i:s', $c1_datetime);
    }

    public static function getStandardMonth($date=false)
    {
        if($date===false)
            $date=time();
        
        $c1_date=strtotime($date);
        
        if($c1_date===false)
            $c1_date=$date;       
        
        return date('M. Y',$c1_date);          
    }
}
?>