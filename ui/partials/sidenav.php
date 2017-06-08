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
          <p><?=$username?></p>
          <a href="/user/publicProfile"><i class="fa fa-user text-success"></i> My Profile</a>
      </div>
    </div>

    <ul class="sidebar-menu">
      <li class="header">MENU</li>
        <li class="<?=($action == 'index') ? 'active' : ''?>"><a href="/"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
        <li class="<?=($action == 'friends') ? 'active' : ''?>"><a href="/user/friends"><i class="fa fa-users"></i> <span>Friends</span></a></li>
        <li class="<?=($action == 'makeFriends') ? 'active' : ''?>"><a href="/user/makeFriends"><i class="fa fa-user-plus"></i> <span>Make Friends</span></a></li>
        <li class="<?=($action == 'suggested') ? 'active' : ''?>"><a href="/user/suggested"><i class="fa fa-tags"></i> <span>Suggested Qualities</span></a></li>
        <li class="<?=($action == 'changePassword') ? 'active' : ''?>"><a href="/user/changePassword"><i class="fa fa-lock"></i> <span>Change Password</span></a></li>
    </ul>
  </section>
</aside>