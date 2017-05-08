<?php
 
include_once 'ui/widget/base/SingleStepWidget.php';

/**
 * User Home Widget
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
    global $currentuser;
    $html = Util::beautify($currentuser->profile->getUsername());
    $html .= '<input required class="rb-rating" type="text" value="" title="">';
    echo 'Welcome to Dashboard '.$html;
  }
}
?>