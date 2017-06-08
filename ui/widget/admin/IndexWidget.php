<?php
 
include_once 'ui/widget/base/SingleStepWidget.php';

/**
 * Admin Dashboard Widget
 */
class IndexWidget extends SingleStepWidget{

  private $UserController;
  private $joinList = array();
  private $invitedList = array();
  private $addedList = array();
  private $droppedList = array();
  private $userByCountries = array();
  private $response = array();

  public function __construct(){
    parent::__construct();
  }
  
  public function init(){
    global $currentuser;

    if(!isset($currentuser))
      Util::redirect("auth","login");

    $this->UserController = new AdminController();

    $this->response['today_users'] = 0;
    $this->response['today_added'] = 0;
    $this->response['today_dropped'] = 0;
    $this->response['today_invited'] = 0;
  }

  public function loadData(){

    $this->invitedList = $this->UserController->getInvitedUsersPerDay();
    
    $this->response['today_invited'] = $this->UserController->getInvitedUsersPerDay(true);

    $this->addedList = $this->UserController->getAddedAsFriendsUsersPerDay();
    
    $this->response['today_added'] = $this->UserController->getAddedAsFriendsUsersPerDay(true);

    $this->droppedList = $this->UserController->getRejectedAsFriendsUsersPerDay();

    $this->response['today_dropped'] = $this->UserController->getRejectedAsFriendsUsersPerDay(true);

    $this->joinList = $this->UserController->getUsersPerDay();

    $this->response['today_users'] = $this->UserController->getTotalUsers();

    $this->userByCountries = $this->UserController->getUsersPerCountry();

    $this->userByCountries = json_encode($this->userByCountries);
  }
  
  public function execute(){}
  
  public function render(){
    
    $html = '<div class="row">
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-aqua">
            <div class="inner">
              <h3>'.$this->response['today_invited'].'</h3>

              <p>Total Invites Today</p>
            </div>
            <div class="icon">
              <i class="ion ion-ios-personadd"></i>
            </div>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-green">
            <div class="inner">
              <h3>'.$this->response['today_added'].'</h3>

              <p>Total Friendships Today</p>
            </div>
            <div class="icon">
              <i class="ion ion-person-stalker"></i>
            </div>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-red">
            <div class="inner">
              <h3>'.$this->response['today_dropped'].'</h3>

              <p>Total Rejections Today</p>
            </div>
            <div class="icon">
              <i class="ion ion-minus-circled"></i>
            </div>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
          <!-- small box -->
          <div class="small-box bg-yellow">
            <div class="inner">
              <h3>'.$this->response['today_users'].'</h3>

              <p>User Registrations</p>
            </div>
            <div class="icon">
              <i class="ion ion-person-add"></i>
            </div>
          </div>
        </div>
        <!-- ./col -->
      </div>
      <div class="row">
        <div class="col-xs-12">
          <!-- Map box -->
          <div class="box box-solid bg-light-blue-gradient">
            <div class="box-header">
              <!-- tools box -->
              <div class="pull-right box-tools">
                <button type="button" class="btn btn-primary btn-sm pull-right" data-widget="collapse" data-toggle="tooltip" title="Collapse" style="margin-right: 5px;">
                  <i class="fa fa-minus"></i></button>
              </div>
              <!-- /. tools -->

              <i class="fa fa-map-marker"></i>

              <h3 class="box-title">
                Users By Country
              </h3>
            </div>
            <div class="box-body">
              <div id="world-map" style="height: 250px; width: 100%;"></div>
            </div>
            <!-- /.box-body-->
          </div>
          <!-- /.box -->
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">People Joining Network by date</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody>
                <tr>
                  <th>Date</th>
                  <th>Users</th>
                </tr>';

        foreach ($this->joinList as $key) {
            $html .= '<tr><td>'.$key['created'].'</td><td>'.$key['users'].'</td></tr>';
        }
        
        $html .= '
              </tbody>
            </table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">People getting Invited as Friend by date</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody>
                <tr>
                  <th>Date</th>
                  <th>Users</th>
                </tr>';

      foreach ($this->invitedList as $key) {
          $html .= '<tr><td>'.$key['date'].'</td><td>'.$key['users'].'</td></tr>';
      }
      
      $html .= '</tbody>
              </table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">People getting added as Friend by date</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody>
                <tr>
                  <th>Date</th>
                  <th>Users</th>
                </tr>';

      foreach ($this->addedList as $key) {
          $html .= '<tr><td>'.$key['date'].'</td><td>'.$key['users'].'</td></tr>';
      }
      
      $html .= '</tbody>
              </tbody>
              </table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">People getting rejected as Friend by date</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody>
                <tr>
                  <th>Date</th>
                  <th>Users</th>
                </tr>';

      foreach ($this->droppedList as $key) {
          $html .= '<tr><td>'.$key['date'].'</td><td>'.$key['users'].'</td></tr>';
      }
      
      $html .= '</tbody>
              </tbody>
              </table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
      </div>';

    $html .= '<script text="text/javascript">$(document).ready(function(){';
    $html .= ' var visitorsData = '.$this->userByCountries.';';
    $html .= "$('#world-map').vectorMap({
    map: 'world_mill_en',
    backgroundColor: 'transparent',
    regionStyle: {
      initial: {
        fill: '#e4e4e4',
        'fill-opacity': 1,
        stroke: 'none',
        'stroke-width': 0,
        'stroke-opacity': 1
      }
    },
    series: {
      regions: [{
        values: visitorsData,
        scale: ['#92c1dc', '#ebf4f9', '#92c1dc'],
        normalizeFunction: 'polynomial'
      }]
    },
    onRegionLabelShow: function (e, el, code) {
      if (typeof visitorsData[code] != 'undefined')
        el.html(el.html() + ': ' + visitorsData[code] + ' users');
    }
  });";

    $html .= '});</script>';

    echo $html;
  }
}
?>