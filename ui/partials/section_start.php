<?php
  global $module, $action;

  $pageTitle = "HOME";
  $pageSubtitle = "";
  if($module == 'user' && $action == 'index') {
    $pageTitle = "Dashboard";
    $pageSubtitle = "Place to see your insights";
  }else if($module == 'user' && $action == 'friends') {
    $pageTitle = "Friends";
  }else if($module == 'user' && $action == 'profile') {
    $pageTitle = "User Profile";
  }else if($module == 'user' && $action == 'search') {
    $pageTitle = "Make Friends";
  }

  $username = Util::beautify($currentuser->profile->getUsername());
  $picture = $currentuser->profile->getPicture() ? 
    $currentuser->profile->getPicture() : "/dist/img/avatar.png";
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      <?=$pageTitle;?>
      <small><?=$pageSubtitle;?></small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="/"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active"><?=$pageTitle;?></li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">