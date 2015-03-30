<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This class controls viewing job confirmation pages on the system.
 *
 * @author Al Zziwa <azziwa@gmail.com>
 * @version 1.1.0
 * @copyright TMIS
 * @created 02/06/2015
 */

class Confirmation extends CI_Controller 
{
	#Constructor to set some default values at class load
	public function __construct()
    {
        parent::__construct();
		$this->load->model('_confirmation');
	}
	
	
	
	# View the confirmation list
	function lists()
	{
		$data = filter_forwarded_data($this);
		$instructions['action'] = array('view'=>'view_job_confirmation_applications', 'approve'=>'issue_job_confirmation_letter', 'verify'=>'verify_job_confirmation_letter', 'post'=>'post_to_new_position');
		check_access($this, get_access_code($data, $instructions));
		
		$data['list'] = $this->_confirmation->get_list($data);
		#Make sure the approver has a signature on file if they are going to issue a letter
		if(!empty($data['list']) && !$this->native_session->get('__signature') && !empty($data['action']) && $data['action']=='approve')
		{
			 $data['msg'] = "WARNING: You need to <a href='".base_url()."profile/user_data'>upload a signature</a> to confirm the teacher posting.";
			 $this->native_session->set('__nosignature','Y');
		}
		$this->load->view('confirmation/list_confirmations', $data); 
	}
	
	
	
		
	
	# Verify the confirmation - just keeping the function name consistent
	function verify()
	{
		$data = filter_forwarded_data($this);
		if(!empty($_POST))
		{
			# Approve or reject a confirmation
			$result = $this->_confirmation->verify($_POST);
			
			$actionPart = current(explode("_", $_POST['action']));
			$actions = array('reject'=>'rejected', 'post'=>'posted', 'approve'=>'approved', 'verify'=>'verified');
			$actionWord = !empty($actions[$actionPart])? $actions[$actionPart]: 'made';
			
			$item = in_array($_POST['action'], array('approve','reject'))? 'posting': 'confirmation';
			$this->native_session->set('msg', ($result['boolean']? "The ".$item." has been ".$actionWord: (!empty($result['msg'])? $result['msg']: "ERROR: The ".$item." could not be ".$actionWord) ));
		}
		else
		{
			# Get list type
			$data['list_type'] = current(explode("_", $data['action']));
			$data['area'] = 'verify_confirmation';
			$this->load->view('confirmation/addons', $data);
		}
	}
	
	
	
	
	
	
	
	
	
	
	
}

/* End of controller file */