<?php
 
include_once 'ui/widget/base/SingleStepWidget.php';

/**
 * Admin User Home Widget
 */
class IndexWidget extends SingleStepWidget{

  public function __construct(){
    parent::__construct();
  }
  
  public function init(){}
  
  public function loadData(){}

  public function execute(){
    global $logger;
    $logger->debug("POST:".print_r($_POST,true));
  }
  
  public function render(){
    
    $html = 'Welcome Admin';
    echo $html;
  }
}
?>