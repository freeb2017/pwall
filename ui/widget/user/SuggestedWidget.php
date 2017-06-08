<?php
 
include_once 'ui/widget/base/SingleStepWidget.php';

/**
 * Suggested Widget
 */
class SuggestedWidget extends SingleStepWidget{

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
    
    $this->userList = $this->UserController->getSuggestedSkillsByFriends($this->user_id);
  }
  
  public function execute(){}
  
  public function render(){
    
    $html = '<div class="row">
        <div class="col-xs-12">
          <div class="box">
            <!-- /.box-header -->
            <div class="box-body">
              <table id="suggested_tbl" class="f-table table table-bordered table-striped compact">';

    $html .= '<thead>
                <tr>
                  <th>Quality</th>
                  <th>Suggested By</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>';

    if($this->userList){
          
      foreach ($this->userList as $key => $value) {
        $html .= '<tr>
                  <td class="text-center">
                    <div class="user-panel">
                        '.$value['quality_name'].'
                    </div>
                  </td>
                  <td>
                    <div class="user-panel">
                        '.ucwords($value['friends']).'
                    </div>
                  </td>
                  <td class="fr-block" data-qid="'.$value['quality_id'].'" data-active="'.$value['is_active'].'">';

                    if(!$value['is_active']) {
                      $html .= '<a class="friend-suggest text-success" data-qid="'.$value['quality_id'].'" data-active="1">Accept</a>';
                      $html .= '<a class="friend-suggest text-danger" data-qid="'.$value['quality_id'].'" data-active="0">Decline</a>';
                    } else {
                      $html .= '<a class="friend-suggest-active text-success" data-qid="'.$value['quality_id'].'" data-active="0"><i>Accepted by you</i></a>';
                    }
                  
        $html .= '</td></tr>';
      }

    } else {
      $html .= '<tr>
                  <td class="text-center">
                    <div class="user-panel">
                        N/A
                    </div>
                  </td>
                  <td class="text-center">
                    <div class="user-panel">
                        N/A
                    </div>
                  </td>
                  <td class="text-center">
                    <div class="user-panel">
                        N/A
                    </div>
                  </td>';
        $html .= '</tr>';
    }

    $html .= "</tbody>";
                
    $html .= '  
              </table>
            </div>
          </div>
        </div>
      </div>';

    echo $html;
  }
}
?>