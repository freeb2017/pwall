<?php
 
include_once 'ui/widget/base/SingleStepWidget.php';

/**
 * Friends Widget
 */
class FriendsWidget extends SingleStepWidget{

  private $UserController;
  private $userList = array();
  private $user_id;
  private $status;
  private $statusList = array();
  private $statusListHtml = '';

  public function __construct(){
    parent::__construct();
  }
  
  public function init(){
    global $currentuser;

    if(!isset($currentuser))
      Util::redirect("auth","login");

    $this->user_id = $currentuser->getUserId();
    $this->UserController = new UserController();
  }

  public function loadData(){
    
    $this->userList = array();

    $users = 
        $this->UserController->getUserListDetails($this->user_id);

    $friends = $this->UserController->getFriendsList($this->user_id);

    if(!empty($users)){
      foreach ($users as $value) {
        if(!empty($friends)){
          $flag = false;
          $temp = $value;
          foreach ($friends as $ivalue) {
            if($ivalue['user_one_id'] == $value['user_id'] || 
              $ivalue['user_two_id'] == $value['user_id']) {

              if(
                  ($ivalue['action_user_id'] != $this->user_id)
                   &&
                  ($ivalue['status'] == "DECLINED")
                )
                continue;
              $temp['action_user_id'] = $ivalue['action_user_id'];
              $temp['status'] = $ivalue['status'];
              $temp['user_one_id'] = $ivalue['user_one_id']; 
              $temp['user_two_id'] = $ivalue['user_two_id'];
              $flag = true;
            }
          }
          if($flag)
            array_push($this->userList, $temp);
        }
      }
    }
  }
  
  public function execute(){}
  
  public function render(){
    
    $html = '<div class="row">
        <div class="col-xs-12">
          <div class="box">
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example2" class="f-table table table-bordered table-striped compact">
                <thead>
                <tr>
                  <th>Member Name</th>
                  <th>Personal Details</th>
                  <th>Qualities</th>
                  <th>Location</th>
                  <th>Friendship</th>
                </tr>
                </thead>
                <tbody>';

    foreach ($this->userList as $value) {
      $html .= '<tr>
                  <td class="text-center">
                    <div class="user-panel tbl-user-panel">
                      <div class="pull-left image">
                        <img src="'.$value['picture'].'" class="img-circle" alt="User Image">
                      </div>
                      <div class="pull-left info">
                        <p>'.$value['username'].'</p>
                        <a href="/user/publicProfile?id='.$value['user_id'].'">
                          <i title="'.$value['gender'].'" class="fa fa-'.strtolower($value['gender']).' text-success"></i> 
                          View/Rate Profile
                        </a>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="text-muted" title="Email">
                      <i class="fa fa-envelope"></i>
                      '.$value['email'].'
                    </div>
                    <div class="text-muted" title="Phone">
                      <i class="fa fa-phone"></i>
                      '.Util::getDefaultValue($value['phone']).'
                    </div>
                  </td>
                  <td>';
              
              foreach (explode(',', $value['qualities']) as $ivalue) {
                  
                  if($ivalue == "Not yet added")
                    $html .= "<span class='label label-warning skill-tbl-label'>";
                  else
                    $html .= "<span class='label label-success skill-tbl-label'>";

                  $html .= $ivalue;
                  
                  $html .= "</span>";
              }
        
        $html .= '</td>
                  <td>'.$value['location'].'</td>
                  <td class="fr-block" data-u1="'.$value['user_one_id'].'" data-u2="'.$value['user_two_id'].'">';

        if($value['status'] == "PENDING" && $value['action_user_id'] == $this->user_id) {
          $html .= '<span class="text-warning" data-status="ACCEPTED" data-uid="'.$value['user_id'].'">Pending for approval</a>';
        } else if($value['status'] == "PENDING" && $value['action_user_id'] != $this->user_id) {
          $html .= '<a class="friend-request text-success" data-status="ACCEPTED" data-uid="'.$value['user_id'].'">Accept</a>';
          $html .= '<a class="friend-request text-danger" data-status="DECLINED" data-uid="'.$value['user_id'].'">Decline</a>';
        } else {
          
          if($value['status'] == "ACCEPTED"){
            $status = "DECLINED";
            $html .= '<a class="friend-request text-danger" data-status="'.$status.'" data-uid="'.$value['user_id'].'">Decline</a>';
          }else{
            $status = "ACCEPTED";
            $html .= '<a class="friend-request text-success" data-status="'.$status.'" data-uid="'.$value['user_id'].'">Accept</a>';
          }
        }
                  
        $html .= '</td>
                </tr>';
    }
                
    $html .= '  </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>';

    echo $html;
  }
}
?>