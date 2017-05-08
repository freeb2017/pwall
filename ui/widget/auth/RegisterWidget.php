<?php
 
include_once 'ui/widget/base/SingleStepWidget.php';

/**
 * Register Widget
 */
class RegisterWidget extends SingleStepWidget{

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

	public function loadData(){
		$countries = $this->UserController->getCountriesAsOptions();
		if(!empty($countries)){
			foreach ($countries as $key => $value) {
				$this->countryList .= 
					"<option value='".$value."'>".$key."</option>";
			}
		}
	}
	
	public function execute(){

		global $logger;

		$logger->debug("POST:".print_r($_POST,true));
		
		list($this->response, $user_id) = 
			$this->UserController->addUser($_POST);

		if($this->response == 'SUCCESS'){
			$this->Auth->login($_POST['email'], $_POST['password']);
			Auth::redirectToPwall();
		}else{
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
			    <p class="login-box-msg">Register a new profile</p>
			    <form action="/auth/register" id="registerForm" method="post" novalidate="novalidate">
			      <div class="form-group has-feedback">
			        <input type="text" class="form-control" placeholder="Full name" name="username" id="username">
			        <span class="glyphicon glyphicon-user form-control-feedback"></span>
			      </div>
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
              	  <div class="form-group has-feedback">
	                  <select class="form-control" name="gender" id="gender">
	                    <optgroup label="Gender">
		                    <option value="MALE">Male</option>
		                    <option value="FEMALE">Female</option>
	                    </optgroup>
	                  </select>
                  </div>
                  <div class="form-group has-feedback">
	                  <select class="form-control" name="country" id="country">
	                    '.$this->countryList.'
	                  </select>
                  </div>
			      <div class="row">
			        <div class="col-xs-12">
			          <button type="submit" class="btn btn-primary btn-block btn-flat">Register</button>
			        </div>
			      </div>
			    </form>
			    <a href="/auth/login" class="text-center">I already have a membership</a>
			    <p class="form-error text-red text-center hide">'.$this->response.'</p>
			  </div>
			  <!-- /.form-box -->
			</div>
			<!-- /.register-box -->';
		
		$html .= '<script text="text/javascript">$(document).ready(function(){';
		$html .= $this->error;
		$html .= '$("#dob").datepicker({
					autoclose: true,
					format: "dd/mm/yyyy",
					startDate: "01/01/1900",
					endDate: "31/12/2000"
					});';
		$html .= '});</script>';

		echo $html;
	}
}
?>