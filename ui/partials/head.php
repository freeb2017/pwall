<?php
  global $module, $action;

  $pageTitle = "HOME";
  if($module == 'user' && $action == 'index') {
    $pageTitle = "Dashboard";
  }else if($module == 'user' && $action == 'friends') {
    $pageTitle = "Friends";
  }else if($module == 'user' && $action == 'profile') {
    $pageTitle = "User Profile";
  }else if($module == 'user' && $action == 'search') {
    $pageTitle = "Make Friends";
  }
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title>PraiseWALL | <?=ucfirst($pageTitle);?></title>
<!-- Tell the browser to be responsive to screen width -->
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

<!-- Custom Favicon -->
<link rel="shortcut icon" href="/favicon.ico">

<!-- Bootstrap 3.3.6 -->
<link rel="stylesheet" href="/bootstrap/css/bootstrap.min.css">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
<!-- Ionicons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
<!-- Theme style -->
<link rel="stylesheet" href="/plugins/datepicker/datepicker3.css">

<link rel="stylesheet" href="/plugins/select2/select2.min.css">

<link rel="stylesheet" href="/dist/css/bootstrap.custom.min.css">
<link rel="stylesheet" href="/dist/css/skin-blue.min.css">
<link rel="stylesheet" href="/style/main.css">
<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->

<!-- build:js /_/js/lib/modernizr/modernizr.js -->
<script src="/_/bower_components/modernizr/modernizr.js"></script>
<!-- endbuild -->
<script src="/plugins/jQuery/jquery-2.2.3.min.js"></script>
</head>