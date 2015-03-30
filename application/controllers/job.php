<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This class controls viewing data on the system.
 *
 * @author Al Zziwa <azziwa@gmail.com>
 * @version 1.1.0
 * @copyright TMIS
 * @created 01/20/2015
 */

class Job extends CI_Controller 
{
	#Constructor to set some default values at class load
	public function __construct()
    {
        parent::__construct();
		$this->load->model('_job');
		$this->load->model('_vacancy');
	}
	
	
	
	
	# Request job confirmation
	function request_confirmation()
	{
		$data = filter_forwarded_data($this);
		check_access($this, 'request_job_confirmation');
		
		if(!empty($data['submit']))
		{
			$result = $this->_job->request_confirmation($data['id']);
			$data['msg'] = $result? "Your job confirmation request has been submitted.": "ERROR: We could not submit your job confirmation request.";
			$data['area'] = "basic_msg";
			$this->load->view('addons/basic_addons', $data);
		}
		else
		{
			$data['job'] = $this->_job->populate_session();
			$data['area'] = "request_confirmation";
			$this->load->view('job/my_profile', $data);
		}
	}
	
	
	# Apply for job promotion
	function apply_for_promotion()
	{
		$data = filter_forwarded_data($this);
		check_access($this, 'apply_for_promotion');
		
		if(!empty($_POST))
		{
			$result = $this->_job->submit_promotion_application($this->input->post(NULL, TRUE));
			$data['msg'] = $result['boolean']? 'Your promotion application has been submitted.': $result['msg'];
			if($result['boolean']) 
			{
				$this->native_session->set('msg', $data['msg']);
				redirect(base_url().'job/view_current');
			}
		}
		
		$this->_job->populate_session();
		$this->load->view('job/promotion_application', $data); 
	}
	
	
	# Current job
	function view_current()
	{
		$data = filter_forwarded_data($this);
		check_access($this, 'view_current_job');
		
		$this->_job->populate_session();
		$this->load->view('job/my_profile', $data);
	}
	
	
	#Submit your current job and request for approval
	function submit_current_job()
	{
		$data = filter_forwarded_data($this);
		
		#User has posted their current job details
		if(!empty($_POST['schoolid']))
		{
			$result = $this->_job->submit_current_job($this->input->post(NULL, TRUE));
			$data['msg'] = $result? "Your job has been submitted for approval": "ERROR: Your job could not be submitted for approval.";
		}
		
		$data['area'] = "submit_current_job";
		$this->load->view('job/addons', $data);
	}
	
	
	
	
	# Previous job
	function view_previous()
	{
		$data = filter_forwarded_data($this);
		check_access($this, 'view_previous_jobs');
		
		$data['list'] = $this->_job->previous_jobs();
		$this->load->view('job/previous_jobs', $data); 
	}
	
		
	
	
	
	
	# View a job list
	function lists()
	{
		$data = filter_forwarded_data($this);
		
		#If the user is logged in or attempting to access the full job report, check their access
		if($this->native_session->get('__user_id') || !empty($data['action']))
		{
			$instructions['action'] = array('view'=>'view_relevant_jobs', 'report'=>'view_job_applications','apply'=>'apply_for_job','saved'=>'view_my_saved_jobs','status'=>'view_job_application_status');
			check_access($this, get_access_code($data, $instructions));
		}
		#Fetch the saved job ids if this is the viewed list
		if(!empty($data['action']) && $data['action'] == 'view') $data['saved_jobs'] = $this->_job->get_saved_jobs();
		
		if(empty($data['action'])) $data['action'] = 'view';
		$data['list'] = $this->_job->get_list(array('action'=>$data['action']));
		
		if(!empty($data['action']) && $data['action'] == 'apply') $data['msg'] = "Search the jobs below to select which one to apply for.";
		
		$viewToLoad = $this->native_session->get('__user_id')? 'job/list_jobs': 'home';
		$this->load->view($viewToLoad, $data);  
	}
	
	
	# Apply for a job
	function apply()
	{
		$data = filter_forwarded_data($this);
		
		# User is applying for the job
		if(!empty($data['action']) && $data['action'] == 'confirm' && !empty($data['id']))
		{
			check_access($this, 'apply_for_job');
			$this->_vacancy->populate_session($data['id']);
			$data['area'] = 'confirm_job_option';
			$viewToLoad = 'job/details';
		}
		else if(!empty($data['action']) && $data['action'] == 'submit' && !empty($data['id']))
		{
			check_access($this, 'apply_for_job');
			$result = $this->_job->apply($data['id']);
			$data['msg'] = $result? "Your job application has been sent.": "ERROR: Your job application could not be confirmed";
			$data['area'] = 'basic_msg';
			$viewToLoad = 'addons/basic_addons';
		}
		# User has just clicked on the apply button from the job
		else if(!empty($data['id']))
		{
			$this->native_session->set('redirect_url', 'job/apply/action/confirm/id/'.$data['id']);
			$data['area'] = 'choose_job_option';
			$viewToLoad = 'addons/basic_addons';
		}
		else
		{
			$data['msg'] = "ERROR: The job details could not be resolved.";
			$data['area'] = 'basic_msg';
			$viewToLoad = 'addons/basic_addons';
		}
		
		$this->load->view($viewToLoad, $data); 
	}
	
	
	
	#Verify the job - in this case save
	function verify()
	{
		$data = filter_forwarded_data($this);
		if(!empty($_POST))
		{
			# Approve or reject a school
			$result = $this->_job->verify($_POST);
			
			$actionPart = current(explode("_", $_POST['action']));
			$actions = array('save'=>'saved', 'archive'=>'archived');
			$actionWord = !empty($actions[$actionPart])? $actions[$actionPart]: 'made';
			$this->native_session->set('msg', ($result['boolean']? "The job has been ".$actionWord: (!empty($result['msg'])? $result['msg']: "ERROR: The job could not be ".$actionWord) ));
		}
		else
		{
			# Get list type
			$data['list_type'] = current(explode("_", $data['action']));
			$data['area'] = 'verify_job';
			$this->load->view('addons/basic_addons', $data);
		}
	}
	
	
	
	
	# Download the list
	function download()
	{
		check_access($this, 'view_job_applications');
		
		$data['list'] = array();
		$list = $this->_job->get_list(array('action'=>'download', 'pagecount'=>DOWNLOAD_LIMIT));
		foreach($list AS $row) array_push($data['list'], array('Applicant'=>$row['applicant_name'], 'School'=>$row['institution_name'], 'Role'=>$row['role_name'], 'Job Title'=>$row['topic'], 'Publish Start'=>date('d-M-Y', strtotime($row['start_date'])), 'Publish End'=>date('d-M-Y', strtotime($row['end_date'])), 'Summary'=>$row['summary'] ));
		
		$data['area'] = 'download_csv';
		$this->load->view('page/download', $data); 
	}
	
	
	
	
}

/* End of controller file */