<?php
 
include_once 'ui/widget/base/SingleStepWidget.php';

/**
 * Change Password Widget
 */
class ChangePasswordWidget extends SingleStepWidget{

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

     $this->userProfile = 
        $this->UserController->loadProfile($this->user_id);
  }

  public function loadData(){}
  
  public function execute(){
    global $logger;
    $logger->debug("POST:".print_r($_POST,true));
    
    try{
      $this->UserController->changePassword(
          $_POST['present_password'], $_POST['password'], $this->user_id
        );
      Util::redirect("user", "changePassword", "Password successfully changed.");
    }catch (Exception $e){
      $this->response = $e->getMessage();
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

            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
        <div class="col-md-9">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#settings" data-toggle="tab">Details</a></li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane active" id="settings">
                <form id="changeForm" action="/user/changePassword" method="post" class="form-horizontal">
                  <div class="form-group">
                    <label for="inputName" class="col-sm-3 control-label">Current Password</label>

                    <div class="col-sm-8">
                      <input type="password" class="form-control" placeholder="Current password" name="present_password" id="present_password">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputName" class="col-sm-3 control-label">New Password</label>

                    <div class="col-sm-8">
                      <input type="password" class="form-control" placeholder="New password" name="password" id="password">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputName" class="col-sm-3 control-label">Confirm Password</label>

                    <div class="col-sm-8">
                      <input type="password" class="form-control" placeholder="Retype password" name="confirm_password" id="confirm_password">
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-sm-offset-5 col-sm-7">
                      <button type="submit" class="btn btn-danger">Submit</button>
                    </div>
                  </div>
                  <p class="form-error text-red text-center hide">'.$this->response.'</p>
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
    $html .= '});</script>';

    echo $html;
  }
}
?>