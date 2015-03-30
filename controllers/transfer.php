<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This class controls viewing transfer pages on the system.
 *
 * @author Al Zziwa <azziwa@gmail.com>
 * @version 1.1.0
 * @copyright TMIS
 * @created 01/20/2015
 */

class Transfer extends CI_Controller 
{
	#Constructor to set some default values at class load
	public function __construct()
    {
        parent::__construct();
		$this->load->model('_transfer');
	}
	
	
	# View transfer lists
	function lists()
	{
		$data = filter_forwarded_data($this);
		$instructions['action'] = array('view'=>'view_transfer_applications', 'institutionapprove'=>'verify_transfer_at_institution_level', 'countyapprove'=>'verify_transfer_at_county_level', 'ministryapprove'=>'verify_transfer_at_ministry_level', 'pca'=>'submit_transfer_pca');
		check_access($this, get_access_code($data, $instructions));
		
		$data['list'] = $this->_transfer->get_list($data);
		#Make sure the approver has a signature on file if they are going to issue a letter
		if(!empty($data['list']) && !$this->native_session->get('__signature') && !empty($data['action']) && in_array($data['action'], array('pca', 'countyapprove')) )
		{
			 $data['msg'] = "WARNING: You need to <a href='".base_url()."profile/user_data'>upload a signature</a> to generate a transfer confirmation document.";
			 $this->native_session->set('__nosignature','Y');
		}
		$this->load->view('transfer/list_transfers', $data); 
	}
	
	
	
	# Cancel a transfer application
	function cancel()
	{
		$data = filter_forwarded_data($this);
		check_access($this, 'cancel_transfer_application');
		$this->load->model('_job');
		
		if(!empty($data['submit']))
		{
			$result = $this->_transfer->cancel();
			$data['msg'] = $result? 'The transfer has been cancelled.': 'ERROR: The transfer could not be cancelled.';
			$data['area'] = 'basic_msg';
			$this->load->view('addons/basic_addons', $data); 
		}
		else
		{
			$data['application'] = $this->_transfer->get_application();
		
			$data['area'] = 'cancel_transfer_application';
			$data['job'] = $this->_job->populate_session();
			$this->load->view('job/my_profile', $data); 
		}
	}
	
	
	# Verify transfer application
	function verify()
	{
		$data = filter_forwarded_data($this);
		if(!empty($_POST))
		{
			# Approve or reject a transfer
			$result = $this->_transfer->verify($_POST);
			
			$actionPart = current(explode("_", $_POST['action']));
			$actions = array('reject'=>'rejected', 'approve'=>'approved');
			$actionWord = !empty($actions[$actionPart])? $actions[$actionPart]: 'made';
			
			$this->native_session->set('msg', ($result['boolean']? "The transfer has been ".$actionWord: (!empty($result['msg'])? $result['msg']: "ERROR: The transfer could not be ".$actionWord) ));
		}
		else
		{
			# Get list type
			$data['list_type'] = current(explode("_", $data['action']));
			$data['area'] = 'verify_transfer';
			$this->load->view('transfer/addons', $data);
		}
	}
	
	
	
	
	# Apply for transfer
	function apply()
	{
		$data = filter_forwarded_data($this);
		check_access($this, 'apply_for_transfer');
		
		#User is submitting application
		if(!empty($_POST))
		{
			$result = $this->_transfer->submit_application($this->input->post(NULL, TRUE));
			$data['msg'] = $result['boolean']? 'Your transfer application has been submitted.': $result['msg'];
			if($result['boolean']) 
			{
				$this->native_session->delete_all(array('school__schools'=>'', 'schoolid'=>'', 'transferdate'=>'','transferreason'=>''));
				$this->native_session->set('msg', $data['msg']);
				redirect(base_url().'transfer/cancel');
			}
		}
		
		$data['currentJob'] = $this->_transfer->get_current_school();
		$this->load->view('transfer/apply', $data);
	}
	
	
}

/* End of controller file */