<?php
  global $currentuser, $flash_message;

  if(!isset($currentuser))
    Util::redirect("auth","login");

  $username = Util::beautify($currentuser->profile->getUsername());
  $picture = $currentuser->profile->getPicture() ? 
    $currentuser->profile->getPicture() : "/dist/img/avatar.png";
  $createdOn = DateUtil::getStandardMonth($currentuser->getCreatedOn());
  // $role = $currentuser->getRole();
?>
<div class="wrapper">
	<!-- Main Header -->
  	<header class="main-header">

    <!-- Logo -->
      <a href="/admin/index" class="logo">
      <span class="logo-mini"><b>P</b>WL</span>
      <span class="logo-lg"><b>Praise</b>WALL</span>
    </a>

    <div class="callout callout-success flash-message hide">
      <p><?=$flash_message;?></p>
    </div>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
      <!-- Navbar Right Menu -->
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="<?=$picture;?>" class="user-image" alt="User Image">
              <span class="hidden-xs">
                <?=$username?>
                <i class="fa fa-caret-down"></i>
              </span>
            </a>
            <ul class="dropdown-menu">
              <li class="user-header">
                <img src="<?=$picture;?>" class="img-circle" alt="User Image">
                <p>
                  <?=$username?>
                  <small>Member since <?=$createdOn?></small>
                </p>
              </li>
                <!-- Menu Footer-->
                <li class="user-footer text-center">
                  <div class="">
                    <a href="/auth/logout" class="btn btn-default btn-flat">Sign out</a>
                  </div>
                </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>