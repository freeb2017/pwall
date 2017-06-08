<?php
 
include_once 'ui/widget/base/SingleStepWidget.php';

/**
 * User Dashboard Widget
 */
class IndexWidget extends SingleStepWidget{

  private $userObj;
  private $UserController;
  private $countryList = array();
  private $error = '';
  private $response = array();
  private $userProfile;
  private $user_id;
  private $skillList = array();
  private $friendship = array();
  private $cuser_id;
  private $qList = array();

  public function __construct(){
    parent::__construct();
  }
  
  public function init(){
    global $currentuser;

    if(!isset($currentuser))
      Util::redirect("auth","login");

    $this->userObj = $currentuser;
    $this->cuser_id = $this->userObj->getUserId();

    if(!isset($_GET['id']) || !$_GET['id']){
      $this->user_id = $this->userObj->getUserId();
    }else{
      $this->user_id = $_GET['id'];
    }
    
    $this->UserController = new UserController();
  }

  public function loadData(){
    
    $this->userObj = 
        $this->UserController->load($this->user_id);

    if(!isset($this->userObj['email']))
      Util::redirect("user","index","Requested Profile does not exists.");

    $this->userProfile = 
        $this->UserController->loadProfile($this->user_id);

    $this->countryList = $this->UserController->getCountriesAsOptions(true);

    $skillIds = $this->UserController->getSkillIdsByUser($this->user_id);
    $this->skillList = array("Not Given" => -1);
    $skills = $this->UserController->getSkillsAsOptions();
    if(!empty($skills)){
      foreach ($skills as $key => $value) {
          if(in_array($value, $skillIds)){
            if(isset($this->skillList["Not Given"]))
              unset($this->skillList["Not Given"]);
            $this->skillList[$key] = $value;
          }
      }
    }

    if($_GET['id']){
      $this->friendship = 
        $this->UserController->checkFriendshipStatus(
          $this->cuser_id, $_GET['id']);
    }

    if($this->friendship['status'] == 'ACCEPTED'){
      $this->qList = 
        $this->UserController->getMergedRatingListByRatedBy(
          $_GET['id'], $this->cuser_id);
    }else{
      $uid = $_GET['id'] ? $_GET['id'] : $this->cuser_id;

      $this->qList = 
        $this->UserController->getOverallRatingsBySkill(
          $uid);
    }

    $this->response['overall'] = $this->UserController->getOverallResultByQualities($this->cuser_id);
    $this->response['improve'] = $this->UserController->getPercentageToImproveUpon($this->cuser_id);
    $this->response['rate'] = $this->UserController->getRateCountsByUser($this->cuser_id);
  }
  
  public function execute(){}
  
  public function render(){
    
    $html = '<div class="row">
        <div class="col-md-3">
        <!-- small box -->
          <div class="small-box bg-maroon disabled">
            <div class="inner">
              <h3>'.($this->response['overall']['rating'] ? $this->response['overall']['rating'] : 0).'/5<span style="font-size: 20px; margin-left:20px">'.$this->response['overall']['count'].' reviews</span></h3>
              <input type="text" class="rate-qualities hide" value="'.$this->response['overall']['rating'].'"></input>
              <p>Overall rating in terms of Qualities</p>
            </div>
            <div class="icon">
              <i class="ion ion-stats-bars"></i>
            </div>
          </div>
          <div class="small-box bg-orange">
            <div class="inner">
              <h3>'.(($this->response['improve']['percentage'] == '0.0' || !$this->response['improve']['percentage']) ? '0' : $this->response['improve']['percentage']).'<sup style="font-size: 20px">%</sup></h3>
              <h4 class="hide">Low ratings: '.$this->response['improve']['users'].'</h4>
              <p>People want Improvement</p>
            </div>
            <div class="icon">
              <i class="ion ion-arrow-graph-down-left"></i>
            </div>
          </div>
          <div class="small-box bg-primary">
            <div class="inner">
              <h3>'.$this->response['rate'].'</h3>
              <p>You rated other\'s Qualities</p>
            </div>
            <div class="icon">
              <i class="ion ion-paper-airplane"></i>
            </div>
          </div>
          <!-- Profile Image -->
          <div class="box box-primary">          
            <div class="box-body box-profile">
              <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                  <b>Active Friends</b> <a class="pull-right">'.$this->UserController->getActiveFriendsCount($this->user_id).'</a>
                </li>
              </ul>';

        if(!$_GET['id']){
          $html .= '<a href="/user/profile" class="btn btn-primary btn-block"><b>Edit Profile</b></a>';
        }

        $html .= '</div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->          
        </div>
        <!-- /.col -->
        <div class="col-md-9">
          <div class="nav-tabs-custom">
            <div class="tab-content">
              <div class="tab-pane active" id="skills">
                <div class="row text-center">';

        if($this->qList){
          foreach ($this->qList as $value) {
                $html .= '
                  <div class="col-xs-4 rate-me-widget">
                    <div class="box box-widget widget-user">
                      <div class="widget-user-header '.$this->UserController->getRatingsThemeConversion($value['rating']).' disabled">
                        <h3 class="widget-user-username">'.$value['quality_name'].'</h3>
                        <h1 class="widget-user-desc">'.$value['count'].'</h1>
                      </div>
                      <div class="box-footer">
                        <div class="row">
                          <div class="col-xs-12">
                            <div class="description-block">
                              <input type="text" class="rate-qualities hide" value="'.$value['rating'].'" data-qid="'.$value['quality_id'].'" data-uid="'.$value['user_id'].'"></input>
                              <span class="description-text">'.$this->UserController->getRatingsConversion($value['rating']).'</span>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="overlay hide">
                        <i class="fa fa-refresh fa-spin"></i>
                      </div>
                    </div>
                  </div>';
          }
        }else{
          $html .= "<h2 class='text-muted'>Qualities not yet added!</h2>";
        }

        $html .= '
                </div>
              </div>
              <!-- /.tab-pane -->
              <div class="tab-pane" id="suggest">
                <div class="row text-center">
                  <div class="col-xs-12 suggest-me-widget">
                    <h1>Coming Soon!</h1>
                  </div>
                </div>
              </div>
            </div>
            <!-- /.tab-content -->
          </div>
          <!-- /.nav-tabs-custom -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
';
  
    $html .= '<script text="text/javascript">$(document).ready(function(){';
    $html .= $this->error;

    $html .= "$('.rate-qualities').rating({
                  'showCaption': true,
                  'stars': '5',
                  'min': '0',
                  'max': '5',
                  'step': '1',
                  'size': 'xs',
                  'starCaptions': {
                    1: 'Poor', 
                    2: 'Beginner', 
                    3: 'Intermediate', 
                    4: 'Very Good',
                    5: 'Expert'
                  }";

    if(($this->user_id != $this->cuser_id)
        &&
        $this->friendship['status'] == 'ACCEPTED'){
      $html .= "});";
    }else{
      $html .= ",'displayOnly': true});";
    }

    $html .= '});</script>';

    echo $html;
  }
}
?>