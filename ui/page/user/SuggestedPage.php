<?php 
include_once 'ui/page/base/BasePage.php';

/**
 * This is the Suggested Qualities page
 */
class SuggestedPage extends BasePage{

  public function __construct(){
    parent::__construct();
    $this->includeRequiredScripts('
      <script src="/js/profile.js"></script>
    ');
  }
  
  public function loadWidgets(){
    
    include_once 'ui/widget/user/SuggestedWidget.php';
    $suggest = new SuggestedWidget();
    $this->callWidget($suggest);
  }
}

?>