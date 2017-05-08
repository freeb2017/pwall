<?php
  global $currentuser;
  $username = Util::beautify($currentuser->profile->getUsername());
  $picture = $currentuser->profile->getPicture() ? 
    $currentuser->profile->getPicture() : "/dist/img/avatar.png";
?>
<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">

  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">

    <!-- Sidebar user panel (optional) -->
    <div class="user-panel">
      <div class="pull-left image">
        <img src="<?=$picture;?>" class="img-circle" alt="User Image">
      </div>
      <div class="pull-left info">
        <p><?=$username?></p>
        <!-- Status -->
        <a href="/user/profile"><i class="fa fa-user text-success"></i> My Profile</a>
      </div>
    </div>

    <!-- Sidebar Menu -->
    <ul class="sidebar-menu">
      <li class="header">MENU</li>
      <!-- Optionally, you can add icons to the links -->
      <li class="active"><a href="/"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>
      <li><a href="#"><i class="fa fa-users"></i> <span>Friends</span></a></li>
      <li><a href="#"><i class="fa fa-user-plus"></i> <span>Make Friends</span></a></li>
    </ul>
    <!-- /.sidebar-menu -->
  </section>
  <!-- /.sidebar -->
</aside>