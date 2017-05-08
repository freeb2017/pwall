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

  public function __construct(){
    parent::__construct();
  }
  
  public function init(){
    global $currentuser;

    if(!isset($currentuser))
      Util::redirect("auth","login");

    if(!isset($_GET['id']) || !$_GET['id']){
      $this->userObj = $currentuser;
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
                  <b>Friends</b> <a class="pull-right">0</a>
                </li>
              </ul>

            </div>
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

            foreach ($this->skillList as $key => $value) {
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
            <ul class="nav nav-tabs">
              <li class="active">
                <a href="#skills" data-toggle="tab">Praise My Qualities</a>
              </li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane active" id="skills">
                
              </div>
              <!-- /.tab-pane -->
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
    $html .= '});</script>';

    echo $html;
  }
}
?>