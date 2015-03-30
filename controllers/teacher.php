<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This class controls viewing teachers on the system.
 *
 * @author Al Zziwa <azziwa@gmail.com>
 * @version 1.1.0
 * @copyright TMIS
 * @created 01/30/2015
 */

class Teacher extends CI_Controller 
{
	#Constructor to set some default values at class load
	public function __construct()
    {
        parent::__construct();
		$this->load->model('_teacher');
		$this->load->model('_person');
	}
	
	
	
	
	# Add a new teacher
	function add()
	{
		$data = filter_forwarded_data($this);
		# Only proceed if just viewing or has access rights
		if(!(((!empty($data['action']) && $data['action'] == 'view')) || check_access($this, 'add_new_teacher', 'boolean'))) 
			check_access($this, 'add_new_teacher');
		
		# Remove any session variables if still in the session.
		if(empty($_POST) && empty($data['edit']) && !(!empty($data['urlaction']) && $data['urlaction'] == 'submit')) 
			$this->_teacher->clear_session();
		
		# If user has posted the form for processing
		if(!empty($_POST))
		{
			# 1. Save or add the data to session?
			
			# a) Just add to session
			if($this->input->post('preview') || isset($_POST['preview'])) 
			{
				$response = $this->_teacher->add_to_session($this->input->post(NULL, TRUE));
				if($response['boolean']) $data['preview'] = "Y";
				else $data['msg'] = $response['msg'];
			}
			# b) Save the data to the database
			else 
			{
				if($this->input->post('userid'))
				{
					$result = $this->_teacher->update($this->input->post(NULL, TRUE));
					if($result['boolean']) 
					{
						$this->_person->add_education_and_qualifications($result['person_id'], $this->input->post(NULL, TRUE));
						$data['forward'] = base_url().'teacher/lists'.(!empty($action)? '/action/'.$action: '/action/view');
						$data['result'] = $result;
					}
				}
				else
				{
					$result = $this->_teacher->add_new($this->input->post(NULL, TRUE));
					if($result['boolean']) 
					{
						$this->_person->add_education_and_qualifications($result['person_id'], $this->input->post(NULL, TRUE));
						$this->_person->submit_application($result['person_id'], array('user_id'=>$result['id'], 'emailaddress'=>$this->native_session->get('emailaddress'), 'first_name'=>$this->native_session->get('firstname') ));
					}
				}
				
				
			}
			
			# 2. Show the appropriate message
			if(!empty($result)) $this->native_session->set('msg', (!empty($result['msg'])? $result['msg']: "The teacher data has been submitted for approval."));
			
			# 3. Redirect if saved successfully
			if(!empty($result['boolean']) && $result['boolean'] && empty($data['id'])) 
			{
				$this->_teacher->clear_session();
				redirect(base_url().'teacher/lists'.(!empty($action)? '/action/'.$action: '/action/view'));
			}
		}
		
		#If editing - and for the first time, load the teacher details into the session 
		if(!empty($data['id']) && empty($data['edit']) && empty($_POST)) $this->_teacher->populate_session($data['id']);
		if(!empty($data['action']) && $data['action'] == 'view') $data['preview'] = "Y";
		
		# Viewing the teacher's profile, collect any documents they have been issued
		if(!empty($data['id']) && !empty($data['action']) && $data['action'] == 'view') $data['documents'] = $this->_teacher->get_documents($data['id']);
		
		# This helps differentiate source of command for shared functions with the teacher's registration functionality
		$this->native_session->set('is_admin_adding_teacher', 'Y');
		
		$this->load->view('teacher/new_teacher', $data); 
	}
	
	
	
	
	# Add the teacher education info to the session
	function add_education()
	{
		$data = filter_forwarded_data($this);
		$data['response'] = $this->_person->add_education('', $this->input->post(NULL, TRUE));
		$data['area'] = "education_list";
		$this->load->view('addons/basic_addons', $data); 
	}
	
	
	
	
	# Add the teacher subject info to the session
	function add_subject()
	{
		$data = filter_forwarded_data($this);
		$data['response'] = $this->_person->add_subject_taught('', $this->input->post(NULL, TRUE));
		$data['area'] = "subject_list";
		$this->load->view('addons/basic_addons', $data); 
	}
	
	
	
	
	# Add the teacher document to the session (and database)
	function add_document()
	{
		$data = filter_forwarded_data($this);
		
		# Process the document addition if one has been successfully uploaded
		if(!empty($_FILES['documenturl__fileurl']) && file_exists($_FILES['documenturl__fileurl']['tmp_name']))
		{
			$this->load->model('_document');
			$upload = $this->_document->upload($_FILES['documenturl__fileurl'], array('type'=>'document'));
			$_POST['documenturl__fileurl'] = $upload['file'];
		}
		
		$data['response'] = $this->_person->add_document('', $this->input->post(NULL, TRUE));
		$data['area'] = "document_list";
		$this->load->view('addons/basic_addons', $data); 
	}
	
	
	
	
	# View a teacher list
	function lists()
	{
		$data = filter_forwarded_data($this);
		if(empty($data['action'])) $data['action'] = 'report';
		$instructions['action'] = array('view'=>'view_teacher_data_changes', 'verify' => 'verify_teacher_application_at_hr_level', 'approve'=>'verify_teacher_application_at_instructor_level', 'report'=>'view_teachers', 'payrollreport'=>'view_payroll_report', 'cimreport'=>'view_cim_report');
		check_access($this, get_access_code($data, $instructions));
		
		$data['list'] = $this->_teacher->get_list($data);
		#Make sure the approver has a signature on file if they are going to generate a certificate
		if(!empty($data['list']) && !$this->native_session->get('__signature') && !empty($data['action']) && $data['action']=='approve')
		{
			 $data['msg'] = "WARNING: You need to <a href='".base_url()."profile/user_data'>upload a signature</a> to approve teacher certification.";
			 $this->native_session->set('__nosignature','Y');
		}
		$this->load->view('teacher/list_teachers', $data); 
	}
	
	
	
	
	# Cancel a teacher addition
	function cancel()
	{
		$data = filter_forwarded_data($this);
		
		$this->_teacher->clear_session();
		redirect(base_url().'teacher/lists'.(!empty($data['action'])? '/action/'.$data['action']: ''));
	}
	
	
	# Verify the teacher (all actions: approve, reject, etc)
	function verify()
	{
		$data = filter_forwarded_data($this);
		if(!empty($_POST))
		{
			# Approve or reject a teacher
			$result = $this->_teacher->verify($_POST);
			
			$actionPart = current(explode("_", $_POST['action']));
			$actions = array('approve'=>'approved', 'reject'=>'rejected', 'archive'=>'archived', 'restore'=>'restored');
			$actionWord = !empty($actions[$actionPart])? $actions[$actionPart]: 'made';
			$this->native_session->set('msg', ($result['boolean']? "The teacher has been ".$actionWord: (!empty($result['msg'])? $result['msg']: "ERROR: The teacher could not be ".$actionWord) ));
		}
		else
		{
			# Get list type
			$data['list_type'] = current(explode("_", $data['action']));
			$data['area'] = 'verify_teacher';
			$this->load->view('addons/basic_addons', $data);
		}
	}
	
	
	
	
	# Download the list
	function download()
	{
		$data = filter_forwarded_data($this);
		check_access($this, 'view_teachers');
		
		$data['list'] = array();
		$list = $this->_teacher->get_list(array('action'=>(!empty($data['action']) && in_array($data['action'], array('payrollreport','cimreport'))? $data['action']: 'download'), 'pagecount'=>DOWNLOAD_LIMIT));
		
		# Payroll Teacher Report
		if(!empty($data['action']) && $data['action'] == 'payrollreport')
		{
			foreach($list AS $row) array_push($data['list'], array('Registration Number'=>$row['file_number'], 'Old UTS Number'=>$row['uts_number'], 'Name'=>$row['name'], 'Title'=>$row['title'], 'Salary Scale'=>$row['salary_scale'], 'Birth Date'=>format_date($row['date_of_birth'],'d-M-Y',''), 'Confirmation Date'=>format_date($row['date_of_confirmation'],'d-M-Y',''), 'First Appointment Date'=>format_date($row['first_appointment_date'],'d-M-Y',''), 'Current Appointment Date'=>format_date($row['current_appointment_date'],'d-M-Y',''), 'Status'=>$row['teacher_status'], 'Qualifications'=>$row['qualifications'], 'Proposed Retirement'=>format_date($row['proposed_retirement'],'d-M-Y',''), 'Retirement Status'=>$row['retirement_status']  ));
			
		}
		# CIM Teacher Report
		else if(!empty($data['action']) && $data['action'] == 'cimreport')
		{
			foreach($list AS $row) array_push($data['list'], array('Registration Number'=>$row['file_number'], 'Old UTS Number'=>$row['uts_number'], 'Name'=>$row['name'], 'Title'=>$row['title'], 'Responsibility'=>$row['responsibility'], 'Substantive Post'=>$row['substantive_post'], 'Birth Date'=>format_date($row['date_of_birth'],'d-M-Y',''), 'Confirmation Date'=>format_date($row['date_of_confirmation'],'d-M-Y',''), 'Location'=>$row['location'], 'School'=>$row['school'], 'School Type'=>$row['school_type'], 'Subjects (Major)'=>$row['subjects_major'], 'Subjects (Minor)'=>$row['subjects_minor'], 'Subjects (Other)'=>$row['subjects_other'], 'First Appointment Date'=>format_date($row['first_appointment_date'],'d-M-Y',''), 'Posting Date'=>format_date($row['posting_date'],'d-M-Y',''), 'Current Appointment Date'=>format_date($row['current_appointment_date'],'d-M-Y',''), 'Status'=>$row['teacher_status'], 'Qualifications'=>$row['qualifications'], 'Proposed Retirement'=>format_date($row['proposed_retirement'],'d-M-Y',''), 'Retirement Status'=>$row['retirement_status'] ));
			
		}
		# Normal Teacher Report
		else
		{
			foreach($list AS $row) array_push($data['list'], array('Teacher Name'=>$row['name'], 'Age'=>$row['age'].str_replace('<br>', ' ', format_age($row['age'],'timeleft')), 'School'=>$row['school'], 'School Address'=>$row['school_address'], 'Email Address'=>$row['email_address'], 'Telephone'=>$row['telephone'], 'Date Added'=>date('d-M-Y', strtotime($row['date_added'])) ));
		}
		$data['area'] = 'download_csv';
		$this->load->view('page/download', $data); 
	}
	
	
	
	
	
	
	
	# View teacher custom report
	function custom_report()
	{
		$data = filter_forwarded_data($this);
		check_access($this, 'view_custom_teacher_report');
		
		# The user has posted the report specifications
		if(!empty($_POST))
		{
			$data['posts'] = $_POST;
			$data['list'] = $this->_teacher->generate_custom_report($this->input->post(NULL, TRUE));
			$data['area'] = 'custom_report_view';
			$this->load->view('teacher/addons', $data);
		}
		# This is the main report page
		else $this->load->view('teacher/custom_report', $data);
	}
	
	
	
	
	
	# Show report specs
	function report_specification()
	{
		$data = filter_forwarded_data($this);
		
		$data['area'] = 'report_specifications';
		$this->load->view('teacher/addons', $data);
	}
	
	
	
}

/* End of controller file */