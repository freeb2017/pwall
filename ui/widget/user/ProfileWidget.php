<?php
 
include_once 'ui/widget/base/SingleStepWidget.php';

/**
 * User Profile Widget
 */
class ProfileWidget extends SingleStepWidget{

  private $userObj;
  private $UserController;
  private $countryList = '';
  private $error = '';
  private $response;
  private $userProfile;
  private $user_id;
  private $skillList = '';

  public function __construct(){
    parent::__construct();
  }
  
  public function init(){
    global $currentuser;

    if(!isset($currentuser))
      Util::redirect("auth","login");

    $this->userObj = $currentuser;
    $this->user_id = $this->userObj->getUserId();
    $this->UserController = new UserController();
  }

  public function loadData(){
    
    $this->userProfile = 
        $this->UserController->loadProfile($this->user_id);

    $this->countryList = '';
    $countries = $this->UserController->getCountriesAsOptions();
    if(!empty($countries)){
      foreach ($countries as $key => $value) {
        if($this->userProfile['country_id'] == $value)
          $this->countryList .= 
            "<option value='".$value."' selected>".$key."</option>";
        else
          $this->countryList .= 
            "<option value='".$value."'>".$key."</option>";
      }
    }

    $skillIds = $this->UserController->getSkillIdsByUser($this->user_id);

    $this->skillList = '';
    $skills = $this->UserController->getSkillsAsOptions();
    if(!empty($skills)){
      foreach ($skills as $key => $value) {
          if(in_array($value, $skillIds))
            $this->skillList .= 
              "<option value='".$value."' selected>".$key."</option>";
        else
          $this->skillList .= 
            "<option value='".$value."'>".$key."</option>";
      }
    }
  }
  
  public function execute(){
    global $logger;
    $logger->debug("POST:".print_r($_POST,true));

    $this->response = 
      $this->UserController->updateUser($this->user_id, $_POST);

    try{
      if($_FILES["picture"]["name"])
        $this->UserController->changePicture($_FILES, $this->user_id);
    } catch ( Exception $e ){
      $this->response = $e->getMessage();
    }

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

              <p class="text-muted text-center">'.$this->userObj->getEmail().'</p>

              <ul class="list-group list-group-unbordered">
                <li class="list-group-item hide">
                  <b>Followers</b> <a class="pull-right">1,322</a>
                </li>
                <li class="list-group-item hide">
                  <b>Following</b> <a class="pull-right">543</a>
                </li>
                <li class="list-group-item">
                  <b>Friends</b> <a class="pull-right">'.$this->UserController->getActiveFriendsCount($this->user_id).'</a>
                </li>
              </ul>

              <a href="/user/friends" class="btn btn-primary btn-block"><b>Friends</b></a>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
        <div class="col-md-9">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#settings" data-toggle="tab">Personal Details</a></li>
            </ul>
            <div class="tab-content">
              <p class="form-error text-red text-center hide">'.$this->response.'</p>
              <div class="tab-pane active" id="settings">
                <form id="profileForm" action="/user/profile" method="post" class="form-horizontal" enctype="multipart/form-data">
                  <div class="form-group">
                    <label for="inputName" class="col-sm-2 control-label">Name</label>

                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="username" placeholder="Full Name" name="username" value="'.$this->userProfile['username'].'">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputEmail" class="col-sm-2 control-label">Gender</label>

                    <div class="col-sm-10">
                      <select class="form-control" name="gender" id="gender">
                        <optgroup label="Gender">
                          <option value="MALE">Male</option>
                          <option value="FEMALE">Female</option>
                        </optgroup>
                      </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputName" class="col-sm-2 control-label">Date of Birth</label>

                    <div class="col-sm-10">
                      <input type="text" class="form-control" placeholder="Date of Birth" name="dob" id="dob" value="'.$this->userProfile['dob'].'">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputName" class="col-sm-2 control-label">Country</label>

                    <div class="col-sm-10">
                      <select class="form-control" name="country" id="country">
                        '.$this->countryList.'
                      </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputName" class="col-sm-2 control-label">Phone</label>

                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="phone" placeholder="Phone Number" name="phone" value="'.$this->userProfile['phone'].'">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputName" class="col-sm-2 control-label">Change Picture</label>

                    <div class="col-sm-10">
                      <input type="file" class="form-control" id="picture" placeholder="Change Picture" name="picture">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputExperience" class="col-sm-2 control-label">Skills</label>

                    <div class="col-sm-10">
                      <select name="skills[]" id="skills" class="form-control skills" data-placeholder="Select your skills" multiple="multiple">
                      '.$this->skillList.'
                      </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputExperience" class="col-sm-2 control-label">Interests</label>

                    <div class="col-sm-10">
                      <textarea class="form-control" id="likes" placeholder="What do you like the most? e.g. things, places, people or culture, etc." name="likes">'.$this->userProfile['likes'].'</textarea>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputExperience" class="col-sm-2 control-label">Dislikes</label>

                    <div class="col-sm-10">
                      <textarea class="form-control" id="dislikes" placeholder="What all things you don\'t like?" name="dislikes">'.$this->userProfile['dislikes'].'</textarea>
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputExperience" class="col-sm-2 control-label">Hobbies</label>

                    <div class="col-sm-10">
                      <textarea class="form-control" id="hobbies" placeholder="What are your hobbies?" name="hobbies">'.$this->userProfile['hobbies'].'</textarea>
                    </div>
                  </div>
                  <div class="form-group hide">
                    <label for="inputSkills" class="col-sm-2 control-label">Skills</label>

                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="inputSkills" placeholder="Skills">
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                      <button type="submit" class="btn btn-danger">Submit</button>
                    </div>
                  </div>
                </form>
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
    $html .= '$("#dob").datepicker({
          autoclose: true,
          format: "yyyy-mm-dd",
          startDate: "1900-01-01",
          endDate: "2010-12-31"
          });
          $("#gender option[value='.$this->userProfile['gender'].']").attr("selected",true);
          $(".img-circle").attr("src","'.$this->userProfile['picture'].'");$(".user-image").attr("src","'.$this->userProfile['picture'].'");
          ';
    $html .= '});</script>';

    echo $html;
  }
}
?>