<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This class controls login access for the system.
 *
 * @author Al Zziwa <azziwa@gmail.com>
 * @version 1.1.0
 * @copyright TMIS
 * @created 01/08/2015
 */

class Account extends CI_Controller 
{
	#Constructor to set some default values at class load
	public function __construct()
    {
        parent::__construct();
		$this->load->model('_validator');
	}
	
	
	#The login page
	public function login()
	{
		$data = filter_forwarded_data($this);
		
		if(!empty($_POST))
		{
			#Is form verified?
			if($this->input->post('verified'))
			{
				#Is user verified?
				$results = $this->_validator->is_valid_account(array('login_name'=>trim($this->input->post('loginusername')), 'login_password'=>trim($this->input->post('loginpassword')) ));
				if($results['boolean'])
				{
					$this->load->model('_permission');
					#If so, assign permissions and redirect to their respective dashboard
					$this->native_session->set('__permissions', $this->_permission->get_user_permission_list($results['user_id']));
					#Log sign-in event
					$this->_logger->add_event(array('log_code'=>'user_login', 'result'=>'success', 'details'=>"userid=".$results['user_id']."|username=".trim($this->input->post('loginusername')) ));
					
					# Go to the user dashboard
					redirect(base_url().get_user_dashboard($this, $results['user_id']));
				}
				# Invalid credentials
				else
				{
					$this->_logger->add_event(array('log_code'=>'user_login', 'result'=>'fail', 'details'=>"username=".trim($this->input->post('loginusername')) ));
					$data['msg'] = "WARNING: Invalid login details.";
				}
			}
			else
			{
				$data['msg'] = "ERROR: Your submission could not be verified.";
			}
		}
		# If already logged in, log out of current session
		else if($this->native_session->get('__user_id'))
		{
			$this->logout($this->native_session->get('__user_id'));
			$data['msg'] = "You have been logged out.";
		}
		
		$this->load->view('account/login', $data);
	}
		
		
	
	# Log out a user
	function logout()
	{
		#Log sign-out event
		$userId = $this->native_session->get('__user_id')? $this->native_session->get('__user_id'): "";
		$email = $this->native_session->get('__email_address')? $this->native_session->get('__email_address'): "";
		$this->_logger->add_event(array('log_code'=>'user_logout', 'result'=>'success', 'details'=>"userid=".$userId."|email=".$email ));
		
		# Set appropriate message - reason for log out.
		$data['msg'] = $this->native_session->get('msg')? get_session_msg($this): "You have been logged out.";
					
		#Remove any set session variables
		$this->native_session->delete_all();
		$this->load->view('account/login', $data);
	}
	
	
	
	
	# Apply for an account
	function apply()
	{
		$data = filter_forwarded_data($this);
		
		if(!empty($_POST))
		{
			$this->load->model('_person');
			#Pass these details to the person object to handle with XSS filter turned on
			$data['result'] = $this->_person->add_profile($this->input->post(NULL, TRUE));
			$data['msg'] = $data['result']['boolean'] && empty($data['result']['msg'])? "Your account has been created but will need approval for you to login.": $data['result']['msg'];
			if($data['result']['boolean'])
			{
				$this->native_session->delete_all(array('person_id'=>'', 'firstname'=>'', 'lastname'=>'', 'role__roles'=>'', 'emailaddress'=>'', 'telephone'=>''));
				$this->native_session->set('msg', $data['msg']);
				redirect(base_url().'account/login');
			}
		}
		
		$this->load->view('application/new_application', $data);
	}
	
	
	
	
	# Forgot the password
	function forgot()
	{
		$data = filter_forwarded_data($this);
		
		if(!empty($_POST))
		{
			$this->load->model('_user');
			$result = $this->_user->recover_password($this->input->post(NULL, TRUE));
			$data['msg'] = $result['boolean']? 'A temporary password has been generated and <br>sent to your registered email and phone. <br><br>Use it to login and change it immediately on your <br>profile page for your security.': $result['msg'];
			
			$data['area'] = 'basic_msg';
			$this->load->view('addons/basic_addons', $data);
		}
		else
		{
			$this->load->view('account/recover_password', $data);
		}
	}
}

/* End of controller file */