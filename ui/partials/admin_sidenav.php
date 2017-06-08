<?php
  global $currentuser, $action;
  $username = Util::beautify($currentuser->profile->getUsername());
  $picture = $currentuser->profile->getPicture() ? 
    $currentuser->profile->getPicture() : "/dist/img/avatar.png";
  // $role = $currentuser->getRole();
?>
<aside class="main-sidebar">
  <section class="sidebar">
    <div class="user-panel">
      <div class="pull-left image">
        <img src="<?=$picture;?>" class="img-circle" alt="User Image">
      </div>
      <div class="pull-left info">
          <h4><?=$username?></h4>
      </div>
    </div>

    <ul class="sidebar-menu">
      <li class="header">MENU</li>
        <li class="<?=($action == 'index') ? 'active' : ''?>"><a href="/admin/index"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
        <li class="<?=($action == 'changePassword') ? 'active' : ''?>"><a href="/user/changePassword"><i class="fa fa-lock"></i> <span>Change Password</span></a></li>
    </ul>
  </section>
</aside>