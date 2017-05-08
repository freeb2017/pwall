<?php
 
include_once 'ui/widget/base/SingleStepWidget.php';

/**
 * Login Widget
 */
class LoginWidget extends SingleStepWidget{

	private $Auth;
	private $error = '';

	public function __construct(){
		parent::__construct();
	}
	
	public function init(){
		$this->Auth = Auth::getInstance();
	}

	public function loadData(){}
	
	public function execute(){
		global $logger;

		$logger->debug("POST:".print_r($_POST,true));
		
		$login_response = $this->Auth->login(
			$_POST['email'], $_POST['password']);

		if($login_response < 1) {
			$logger->debug("Login failed");
			$this->error = "$('.form-error').removeClass('hide');";
		} else {
			Auth::redirectToPwall();
		}
	}
	
	public function render(){
		$html = 
			'<div class="login-box">
			  <div class="login-logo">
			    <a href="#"><b>Praise</b>WALL</a>
			  </div>
			  <!-- /.login-logo -->
			  <div class="login-box-body">
			    <p class="login-box-msg">Sign in to start your journey</p>

			    <form action="/auth/login" method="post">
			      <div class="form-group has-feedback">
			        <input type="email" class="form-control" placeholder="Email" name="email">
			        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
			      </div>
			      <div class="form-group has-feedback">
			        <input type="password" class="form-control" placeholder="Password" name="password">
			        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
			      </div>
			      <div class="row">
			        <div class="col-xs-12">
			          <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
			        </div>
			      </div>
			    </form>
			    <a href="/auth/register" class="text-center">Register a new membership</a>
			    <p class="form-error text-red text-center hide">Invalid Username or Password</p>
			  </div>
			  <!-- /.login-box-body -->
			</div>
			<!-- /.login-box -->';

		$html .= '<script text="text/javascript">$(document).ready(function(){'.$this->error.'});</script>';
		
		echo $html;
	}
}
?>