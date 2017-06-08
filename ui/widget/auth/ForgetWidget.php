<?php
 
include_once 'ui/widget/base/SingleStepWidget.php';

/**
 * Forget Widget
 */
class ForgetWidget extends SingleStepWidget{

	private $UserController;
	private $countryList = '';
	private $error = '';
	private $response;
	private $Auth;

	public function __construct(){
		parent::__construct();
	}
	
	public function init(){
		$this->Auth = Auth::getInstance();
		$this->UserController = new UserController();
	}

	public function loadData(){}
	
	public function execute(){

		global $logger;

		$logger->debug("POST:".print_r($_POST,true));
		
		try{
			$this->UserController->forgetPassword($_POST);
			Util::redirect("auth", "login");
		}catch (Exception $e){
			$this->response = $e->getMessage();
			$this->error = "$('.form-error').removeClass('hide');";
		}	
	}
	
	public function render(){

		$html = 
			'<div class="register-box">
			  <div class="register-logo">
			    <a href="#"><b>Praise</b>WALL</a>
			  </div>

			  <div class="register-box-body">
			    <p class="login-box-msg">Reset your Password</p>
			    <form action="/auth/forget" id="forgetForm" method="post" novalidate="novalidate">
			      <div class="form-group has-feedback">
			        <input type="email" class="form-control" placeholder="Email" name="email" id="email">
			        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
			      </div>
			      <div class="form-group has-feedback">
			        <input type="password" class="form-control" placeholder="Password" name="password" id="password">
			        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
			      </div>
			      <div class="form-group has-feedback">
			        <input type="password" class="form-control" placeholder="Retype password" name="confirm_password" id="confirm_password">
			        <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
			      </div>
			      <div class="form-group has-feedback">
                	<input type="text" class="form-control" placeholder="Date of Birth" name="dob" id="dob">
                  	<span class="glyphicon glyphicon-calendar form-control-feedback"></span>
              	  </div>
			      <div class="row">
			        <div class="col-xs-12">
			          <button type="submit" class="btn btn-primary btn-block btn-flat">Submit</button>
			        </div>
			      </div>
			    </form>
			    <a href="/auth/login" class="text-center">I just remembered my password</a>
			    <p class="form-error text-red text-center hide">'.$this->response.'</p>
			  </div>
			  <!-- /.form-box -->
			</div>
			<!-- /.register-box -->';
		
		$html .= '<script text="text/javascript">$(document).ready(function(){';
		$html .= $this->error;
		$html .= '$("#dob").datepicker({
					autoclose: true,
					format: "yyyy-mm-dd",
					startDate: "1900-01-01",
					endDate: "2000-12-31"
					});';
		$html .= '});</script>';

		echo $html;
	}
}
?>