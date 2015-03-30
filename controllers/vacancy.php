<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This class controls viewing vacancy pages on the system.
 *
 * @author Al Zziwa <azziwa@gmail.com>
 * @version 1.1.0
 * @copyright TMIS
 * @created 01/20/2015
 */

class Vacancy extends CI_Controller 
{
	#Constructor to set some default values at class load
	public function __construct()
    {
        parent::__construct();
		$this->load->model('_vacancy');
	}
	
	
	
	
	#Verify the vacancy before proceeding to the next stage
	function verify()
	{
		$data = filter_forwarded_data($this);
		if(!empty($_POST))
		{
			# Approve or reject a vacancy
			$result = $this->_vacancy->verify($_POST);
			
			$actionPart = current(explode("_", $_POST['action']));
			$actions = array('approve'=>'approved', 'reject'=>'rejected', 'archive'=>'archived', 'restore'=>'restored', 'publish'=>'published');
			$actionWord = !empty($actions[$actionPart])? $actions[$actionPart]: 'made';
			$this->native_session->set('msg', ($result['boolean']? "The vacancy has been ".$actionWord: (!empty($result['msg'])? $result['msg']: "ERROR: The vacancy could not be ".$actionWord) ));
		}
		else
		{
			# Get list type
			$data['list_type'] = current(explode("_", $data['action']));
			$data['area'] = 'verify_vacancy';
			$this->load->view('addons/basic_addons', $data);
		}
	}
	
	# Add a new job
	function add()
	{
		$data = filter_forwarded_data($this);
		check_access($this, 'add_new_job');
		# Remove any session variables if still in the session.
		$this->_vacancy->clear_session();
		
		# If the user has posted the vacancy details
		if(!empty($_POST))
		{
			# Editing vacancy
			if($this->input->post('vacancyid'))
			{
				$data['result'] = $this->_vacancy->update($this->input->post('vacancyid'), $this->input->post(NULL, TRUE));
			}
			# New vacancy
			else 
			{
				$data['result'] = $this->_vacancy->add_new($this->input->post(NULL, TRUE));
			}
			
			$data['vacancy_id'] = !empty($data['result']['id'])? $data['result']['id']: '';
			$data['msg'] = $data['result']['boolean'] && empty($data['result']['msg'])? "Your job details have been saved.": $data['result']['msg'];
			# Redirect to appropriate page if successful
			$this->native_session->set('msg', $data['msg']);
			if($data['result']['boolean'] && $this->input->post('forwardurl')) redirect(base_url().$this->input->post('forwardurl'));
		}
		
		#If editing, load the id details into the session for the first time 
		if(!empty($data['id']) && empty($_POST)) $this->_vacancy->populate_session($data['id']);
		$this->load->view('vacancy/new_vacancy', $data); 
	}
	
	
	
	# View a job list
	function lists()
	{
		$data = filter_forwarded_data($this);
		$instructions['action'] = array('publish'=>'publish_job_notices', 'verify'=>'verify_job_notices', 'archive'=>'archive_job_notices', 'report'=>'view_jobs');
		check_access($this, get_access_code($data, $instructions));
		
		$data['list'] = $this->_vacancy->get_list($data);
		$this->load->view('vacancy/list_vacancies', $data); 
	}
	
	
	
	# View a job's details
	function details()
	{
		$data = filter_forwarded_data($this);
		
		if(!empty($data['id']))
		{
			$data['details'] = $this->_vacancy->get_details($data['id']);
		}
		else
		{
			$data['msg'] = "ERROR: We could not find the vacancy details.";
		}
		
		$this->load->view('vacancy/details', $data); 
	}
	
	
	
	
	# Download the list
	function download()
	{
		check_access($this, 'view_jobs');
		
		$data['list'] = array();
		$list = $this->_vacancy->get_list(array('action'=>'download', 'pagecount'=>DOWNLOAD_LIMIT));
		foreach($list AS $row) array_push($data['list'], array('School'=>$row['institution_name'], 'Role'=>$row['role_name'], 'Job Title'=>$row['topic'], 'Publish Start'=>date('d-M-Y', strtotime($row['start_date'])), 'Publish End'=>date('d-M-Y', strtotime($row['end_date'])), 'Summary'=>$row['summary'] ));
		
		$data['area'] = 'download_csv';
		$this->load->view('page/download', $data); 
	}
	
	
	
	
}

/* End of controller file */