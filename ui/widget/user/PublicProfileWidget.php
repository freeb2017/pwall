<?php
 
include_once 'ui/widget/base/SingleStepWidget.php';

/**
 * User Profile Widget
 */
class PublicProfileWidget extends SingleStepWidget{

  private $userObj;
  private $UserController;
  private $countryList = array();
  private $error = '';
  private $response;
  private $userProfile;
  private $user_id;
  private $skillList = array();
  private $friendship = array();
  private $cuser_id;
  private $qList = array();
  private $haveList = array();
  private $renderList = '';

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
    $suggested = $this->UserController->getSuggestedList($this->user_id, $this->cuser_id);
    $this->skillList = array("Not Given" => -1);
    $skills = $this->UserController->getSkillsAsOptions();
    $this->renderList = '';
    if(!empty($skills)){
      foreach ($skills as $key => $value) {
          if(in_array($value, $skillIds)){
            if(isset($this->skillList["Not Given"]))
              unset($this->skillList["Not Given"]);
            $this->haveList[$key] = $value;
          }else{
            $this->skillList[$key] = $value;
              if(in_array($value, $suggested))
                $this->renderList .= 
                  "<option value='".$value."' selected>".$key."</option>";
              else
                $this->renderList .= 
                  "<option value='".$value."'>".$key."</option>";
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
  }
  
  public function execute(){
    global $logger;
    $logger->debug("POST:".print_r($_POST,true));
    $this->response = 
      $this->UserController->updateUser($this->user_id, $_POST);

    if($this->response != 'SUCCESS'){
      $this->error = "$('.form-error').removeClass('hide');";
    } 
  }
  
  public function render(){
    $html = '<div class="row">
        <div class="col-md-3">
          <!-- Profile Image -->
          <div class="box box-primary">
            <div class="box-body box-profile">
              <img class="profile-user-img img-responsive img-circle" src="'.$this->userProfile['picture'].'" alt="User profile picture">
              <h3 class="profile-username text-center">'.Util::beautify($this->userProfile['username']).'</h3>

              <p class="text-muted text-center" title="Email"><i class="fa   fa-envelope"></i> '.$this->userObj['email'].'</p>

              <p class="text-muted text-center" title="Phone"><i class="fa  fa-phone"></i> '.Util::getDefaultValue($this->userProfile['phone']).'</p>

              <p class="text-muted text-center" title="Date of Birth"><i class="fa  fa-calendar"></i> '.DateUtil::getStandardDate($this->userProfile['dob']).'</p>

              <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                  <b>Friends</b> <a class="pull-right">'.$this->UserController->getActiveFriendsCount($this->user_id).'</a>
                </li>
              </ul>';

        if(!$_GET['id']){
          $html .= '<a href="/user/profile" class="btn btn-primary btn-block"><b>Edit Profile</b></a>';
        }

        $html .= '</div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->

          <!-- About Me Box -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">About Me</h3>
              <span class="pull-right" title="'.$this->userProfile['gender'].'">
                <i class="fa fa-'.strtolower($this->userProfile['gender']).'"></i>
              </span>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <strong><i class="fa fa-map-signs margin-r-5"></i> Interests</strong>
              <p class="text-muted">'.Util::getDefaultValue($this->userProfile['likes']).'</p>

              <hr>

              <strong><i class="fa fa-map-marker margin-r-5"></i> Location</strong>
              <p class="text-muted">'.$this->countryList[$this->userProfile['country_id']].'</p>

              <hr>

              <strong><i class="fa fa-bullhorn margin-r-5"></i> Skills</strong>
              <p>';

            foreach ($this->haveList as $key => $value) {
              if($value == -1)
                $html .= "<span class='label label-danger skill-label'>$key</span>";
              else
                $html .= "<span class='label label-success skill-label'>$key</span>";
            }

            $html .='</p>

              <hr>

              <strong><i class="fa fa-heart margin-r-5"></i> Hobbies</strong>
              <p class="text-muted">'.Util::getDefaultValue($this->userProfile['hobbies']).'</p>

              <hr>
              
              <strong><i class="fa fa-heart-o margin-r-5"></i> Dislikes</strong>
              <p class="text-muted">'.Util::getDefaultValue($this->userProfile['dislikes']).'</p>

            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
        <div class="col-md-9">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs qualities-tab">
              <li class="active praise">
                <a href="#skills" data-toggle="tab">Praise Qualities</a>
              </li>
              <li class="suggest '.(($this->cuser_id != $this->user_id) ? '' : 'hide').'">
                <a href="#suggest" data-toggle="tab">Suggested Qualities By You</a>
              </li>
            </ul>
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
                              <input type="text" class="rate-qualities hide" value="'.$value['rating'].'" data-qid="'.$value['quality_id'].'" data-uid="'.$value['user_id'].'"></input>';
                
                if($this->friendship['status'] != 'ACCEPTED')
                  $html .= '<span class="description-text">'.$this->UserController->getRatingsConversion($value['rating']).'</span>';
                
                $html .= '</div>
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
                  <div class="form-group pull-left">
                    <label for="inputExperience" class="col-sm-2 control-label">Suggest Qualities</label>

                    <div class="col-sm-10">
                      <select name="suggestskills[]" id="suggestskills" class="form-control suggestskills" data-placeholder="Select skills to suggest" multiple="multiple" data-uid="'.$this->user_id.'">';

        $html .= $this->renderList;

        $html .= '</select>
                    </div>
                  </div>
                  <p class="form-error text-red text-center hide">'.$this->response.'</p>
                  <p class="text-center w100 m10 pull-left">Having the following qualities: ';

            foreach ($this->haveList as $key => $value) {
              if($value == -1)
                $html .= "<span class='label label-danger skill-label'>$key</span>";
              else
                $html .= "<span class='label label-success skill-label'>$key</span>";
            }

            $html .='</p>
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