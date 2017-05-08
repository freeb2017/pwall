<?php
  global $action;
  $username = Util::beautify($currentuser->profile->getUsername());
  $picture = $currentuser->profile->getPicture() ? 
    $currentuser->profile->getPicture() : "/dist/img/avatar.png";
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      <?=Util::getPageTitle($pageTitle);?>
      <small><?=Util::getPageSubTitle($pageSubtitle);?></small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="/"><i class="fa fa-dashboard"></i> Home</a></li>
      <? if($action == "publicProfile") { ?>
        <li><a href="/user/friends">Friends</a></li>
      <? } ?>
      <li class="active"><?=Util::getPageTitle($pageTitle);?></li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">