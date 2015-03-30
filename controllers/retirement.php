<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This class controls viewing retirement pages on the system.
 *
 * @author Al Zziwa <azziwa@gmail.com>
 * @version 1.1.0
 * @copyright TMIS
 * @created 01/20/2015
 */

class Retirement extends CI_Controller 
{
	# Constructor to set some default values at class load
	public function __construct()
    {
        parent::__construct();
		$this->load->model('_retirement');
	}
	
	
	# View retirement application list
	function lists()
	{
		$data = filter_forwarded_data($this);
		if(empty($data['action'])) $data['action'] = 'view';
		$instructions['action'] = array('view'=>'view_retirement_applications', 'approve'=>'verify_retirement_application', 'report'=>'view_retirements');
		check_access($this, get_access_code($data, $instructions));
		
		$data['list'] = $this->_retirement->get_list($data);
		#Make sure the approver has a signature on file if they are going to issue a letter
		if(!empty($data['list']) && !$this->native_session->get('__signature') && !empty($data['action']) && $data['action'] == 'approve')
		{
			 $data['msg'] = "WARNING: You need to <a href='".base_url()."profile/user_data'>upload a signature</a> to generate a retirement document.";
			 $this->native_session->set('__nosignature','Y');
		}
		$this->load->view('retirement/list_retirements', $data); 
	}
	
	
	
	# Cancel a retirement application
	function cancel()
	{
		$data = filter_forwarded_data($this);
		check_access($this, 'cancel_retirement_application');
		$this->load->model('_job');
		
		if(!empty($data['submit']))
		{
			$result = $this->_retirement->cancel();
			$data['msg'] = $result? 'The retirement application has been cancelled.': 'ERROR: The retirement application could not be cancelled.';
			$data['area'] = 'basic_msg';
			$this->load->view('addons/basic_addons', $data); 
		}
		else
		{
			$data['application'] = $this->_retirement->get_application();
		
			$data['area'] = 'cancel_retirement_application';
			$data['job'] = $this->_job->populate_session();
			$this->load->view('job/my_profile', $data); 
		}
	}
	
	
	# Verify a retirement application
	function verify()
	{
		$data = filter_forwarded_data($this);
		if(!empty($_POST))
		{
			# Approve or reject a retirement
			$result = $this->_retirement->verify($_POST);
			
			$actionPart = current(explode("_", $_POST['action']));
			$actions = array('reject'=>'rejected', 'approve'=>'approved');
			$actionWord = !empty($actions[$actionPart])? $actions[$actionPart]: 'made';
			
			$this->native_session->set('msg', ($result['boolean']? "The retirement has been ".$actionWord: (!empty($result['msg'])? $result['msg']: "ERROR: The retirement could not be ".$actionWord) ));
		}
		else
		{
			# Get list type
			$data['list_type'] = current(explode("_", $data['action']));
			$data['area'] = 'verify_retirement';
			$this->load->view('retirement/addons', $data);
		}
	}
	
	
	
	# Apply for retirement
	function apply()
	{
		$data = filter_forwarded_data($this);
		check_access($this, 'apply_to_retire');
		
		if(!empty($_POST)) 
		{
			$result = $this->_retirement->submit_application($this->input->post(NULL, TRUE));
			$data['msg'] = $result['boolean']? 'Your retirement application has been submitted.': $result['msg'];
			
			if($result['boolean']) 
			{
				$this->native_session->set('msg', $data['msg']);
				redirect(base_url().'retirement/cancel');
			}
		}
		
		$data['current_application'] = $this->_retirement->details($this->native_session->get('__user_id'));
		$this->load->view('retirement/apply', $data); 
	}
	
	
	
	
	# Download the list
	function download()
	{
		check_access($this, 'view_retirements');
		
		$data['list'] = array();
		$list = $this->_retirement->get_list(array('action'=>'download', 'pagecount'=>DOWNLOAD_LIMIT));
		foreach($list AS $row) array_push($data['list'], array('File Number'=>$row['file_number'], 'Teacher Name'=>$row['teacher_name'], 'Job Title'=>$row['job'], 'Planned Date'=>date('d-M-Y', strtotime($row['proposed_date'])).($row['confirmed_date'] != '0000-00-00'? ' (ACTUAL: '.date('d-M-Y', strtotime($row['confirmed_date'])).')': ''), 'Age'=>$row['age'], 'Reason'=>$row['retiree_reason'] ));
		
		$data['area'] = 'download_csv';
		$this->load->view('page/download', $data); 
	}
	
	
	
	
	
}

/* End of controller file */