<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This class controls viewing schools on the system.
 *
 * @author Al Zziwa <azziwa@gmail.com>
 * @version 1.1.0
 * @copyright TMIS
 * @created 01/20/2015
 */

class School extends CI_Controller 
{
	#Constructor to set some default values at class load
	public function __construct()
    {
        parent::__construct();
		$this->load->model('_school');
	}
	
	
	# View current school where this teacher works
	function view_current()
	{
		$data = filter_forwarded_data($this);
		check_access($this, 'view_current_school');
		
		$data['school'] = $this->_school->get_current();
		$this->load->view('school/current', $data); 
	}
	
	
	
	# View previous schools where the teacher has worked before
	function view_previous()
	{
		$data = filter_forwarded_data($this);
		check_access($this, 'view_previous_schools');
		
		$data['list'] = $this->_school->get_previous();
		$this->load->view('school/previous', $data); 
	}
	
	
	
	# Add a new school
	function add()
	{
		$data = filter_forwarded_data($this);
		check_access($this, 'add_new_school');
		# Remove any session variables if still in the session.
		if(empty($_POST)) $this->_school->clear_session();

		
		# If user has posted the form for processing
		if(!empty($_POST))
		{
			$data['result'] = $this->input->post('schoolid')? $this->_school->update($this->input->post('schoolid'), $this->input->post(NULL, TRUE)): $this->_school->add_new($this->input->post(NULL, TRUE));
				
			if($this->input->post('schoolid')) $data['schoolid'] = $this->input->post('schoolid');
			$data['msg'] = $data['result']['boolean'] && empty($data['result']['msg'])? "The school details have been saved.": $data['result']['msg'];
			$this->_school->clear_session();
			# Redirect to appropriate page if successful
			$this->native_session->set('msg', $data['msg']);
			# Go to verification page if user has permission
			if(check_access($this, 'verify_school_data_updates', 'boolean')) $data['action'] = 'verify';
			
			if($data['result']['boolean'] && !$this->input->post('schoolid')) redirect(base_url().'school/lists'.(!empty($data['action']) && $data['action'] !='view'? '/action/'.$data['action']: ''));
			else if($this->input->post('schoolid')) $data['forward'] = base_url().$this->input->post('forward');
			
		}
		
		#If editing, load the id details into the session for the first time 
		if(!empty($data['id']) && empty($_POST)) $this->_school->populate_session($data['id']);
		$this->load->view('school/new_school', $data); 
	}
	
	
	
	
	
	# View a school list
	function lists()
	{
		$data = filter_forwarded_data($this);
		if(empty($data['action'])) $data['action'] = 'view';
		$instructions['action'] = array('view'=>'view_school_data_changes', 'report'=>'view_schools', 'verify'=>'verify_school_data_updates');
		check_access($this, get_access_code($data, $instructions));
		
		$data['list'] = $this->_school->get_list($data);
		$this->load->view('school/list_schools', $data); 
	}
	
	
	
	# Download the list
	function download()
	{
		check_access($this, 'view_schools');
		
		$data['list'] = array();
		$list = $this->_school->get_list(array('action'=>'download', 'pagecount'=>DOWNLOAD_LIMIT));
		foreach($list AS $row) array_push($data['list'], array('School Name'=>$row['name'], 'Date Registered'=>$row['date_registered'], 'School Type'=>$row['school_type'], 'Address'=>$row['addressline'].' '.$row['county'].' '.$row['district'].' '.$row['country'], 'Email Address'=>$row['email_address'], 'Telephone'=>$row['telephone'] ));
		
		$data['area'] = 'download_csv';
		$this->load->view('page/download', $data); 
	}
	
	
	
	
	# Verify the school before proceeding to the next stage
	function verify()
	{
		$data = filter_forwarded_data($this);
		if(!empty($_POST))
		{
			# Approve or reject a school
			$result = $this->_school->verify($_POST);
			
			$actionPart = current(explode("_", $_POST['action']));
			$actions = array('approve'=>'approved', 'reject'=>'rejected', 'archive'=>'archived', 'restore'=>'restored');
			$actionWord = !empty($actions[$actionPart])? $actions[$actionPart]: 'made';
			$this->native_session->set('msg', ($result['boolean']? "The school has been ".$actionWord: (!empty($result['msg'])? $result['msg']: "ERROR: The school could not be ".$actionWord) ));
		}
		else
		{
			# Get list type
			$data['list_type'] = current(explode("_", $data['action']));
			$data['area'] = 'verify_school';
			$this->load->view('addons/basic_addons', $data);
		}
	}
}

/* End of controller file */