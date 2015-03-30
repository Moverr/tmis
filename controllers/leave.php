<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This class controls viewing leave pages on the system.
 *
 * @author Al Zziwa <azziwa@gmail.com>
 * @version 1.1.0
 * @copyright TMIS
 * @created 01/20/2015
 */

class Leave extends CI_Controller 
{
	#Constructor to set some default values at class load
	public function __construct()
    {
        parent::__construct();
		$this->load->model('_leave');
	}
	
	
	# View leave lists
	function lists()
	{
		$data = filter_forwarded_data($this);
		$instructions['action'] = array('view'=>'view_leave_applications', 'approve'=>'verify_leave_at_county_level', 'verify'=>'verify_leave_at_ministry_level', 'send'=>'prepare_leave_verification_letter');
		check_access($this, get_access_code($data, $instructions));
		
		$data['list'] = $this->_leave->get_list($data);
		#Make sure the approver has a signature on file if they are going to issue a letter
		if(!empty($data['list']) && !$this->native_session->get('__signature') && !empty($data['action']) && $data['action']=='send')
		{
			 $data['msg'] = "WARNING: You need to <a href='".base_url()."profile/user_data'>upload a signature</a> to send a leave confirmation letter.";
			 $this->native_session->set('__nosignature','Y');
		}
		$this->load->view('leave/list_leaves', $data); 
	}
	
	
	
	# Cancel a leave application
	function cancel()
	{
		$data = filter_forwarded_data($this);
		check_access($this, 'cancel_leave_application');
		$this->load->model('_job');
		
		if(!empty($data['submit']))
		{
			$result = $this->_leave->cancel();
			$data['msg'] = $result? 'The leave has been cancelled.': 'ERROR: The leave could not be cancelled.';
			$data['area'] = 'basic_msg';
			$this->load->view('addons/basic_addons', $data); 
		}
		else
		{
			$data['application'] = $this->_leave->get_application();
		
			$data['area'] = 'cancel_leave_application';
			$data['job'] = $this->_job->populate_session();
			$this->load->view('job/my_profile', $data); 
		}
	}
	
	
	# Verify leave application
	function verify()
	{
		$data = filter_forwarded_data($this);
		if(!empty($_POST))
		{
			# Approve or reject a leave
			$result = $this->_leave->verify($_POST);
			
			$actionPart = current(explode("_", $_POST['action']));
			$actions = array('reject'=>'rejected', 'approve'=>'approved');
			$actionWord = !empty($actions[$actionPart])? $actions[$actionPart]: 'made';
			
			$this->native_session->set('msg', ($result['boolean']? "The leave has been ".$actionWord: (!empty($result['msg'])? $result['msg']: "ERROR: The leave could not be ".$actionWord) ));
		}
		else
		{
			# Get list type
			$data['list_type'] = current(explode("_", $data['action']));
			$data['area'] = 'verify_leave';
			$this->load->view('leave/addons', $data);
		}
	}
	
	
	
	# Apply for leave
	function apply()
	{
		$data = filter_forwarded_data($this);
		check_access($this, 'apply_for_leave');
		
		#User is submitting application
		if(!empty($_POST))
		{
			$result = $this->_leave->submit_application($this->input->post(NULL, TRUE));
			$data['msg'] = $result['boolean']? 'Your leave application has been submitted.': $result['msg'];
			if($result['boolean']) 
			{
				$this->native_session->delete_all(array('leavestartdate'=>'','leaveenddate'=>'','leavereason'=>''));
				$this->native_session->set('msg', $data['msg']);
				redirect(base_url().'leave/cancel');
			}
		}
		
		$data['currentJob'] = $this->_leave->get_current_school();
		$this->load->view('leave/apply', $data); 
	}
	
	
}

/* End of controller file */