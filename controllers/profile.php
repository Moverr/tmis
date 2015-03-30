<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This class controls viewing profile pages on the system.
 *
 * @author Al Zziwa <azziwa@gmail.com>
 * @version 1.1.0
 * @copyright TMIS
 * @created 01/20/2015
 */

class Profile extends CI_Controller 
{
	#Constructor to set some default values at class load
	public function __construct()
    {
        parent::__construct();
	}
	
	
	# Update teacher profile
	function teacher_data()
	{
		$data = filter_forwarded_data($this);
		check_access($this, 'update_my_teacher_profile');
		$this->load->model('_teacher');
		$this->load->model('_person');
		
		# Set variables to note that the teacher data is being edited
		$data['editing_teacher'] = 'Y';
		$data['id'] = $this->native_session->get('__user_id');
		
		# Remove any session variables if still in the session.
		if(empty($_POST) && empty($data['edit']) && !(!empty($data['urlaction']) && $data['urlaction'] == 'submit')) $this->_teacher->clear_session();
		
		# If user has posted the form for processing
		if(!empty($_POST))
		{
			# 1. Save or add the data to session?
			if($this->input->post('preview') || isset($_POST['preview'])) 
			{
				$response = $this->_teacher->add_to_session($this->input->post(NULL, TRUE));
				if($response['boolean']) $data['preview'] = "Y";
				else $data['msg'] = $response['msg'];
			}
			else 
			{
				if($this->input->post('userid'))
				{
					$result = $this->_teacher->update($this->input->post(NULL, TRUE));
					if($result['boolean']) 
					{
						$this->_person->add_education_and_qualifications($result['person_id'], $this->input->post(NULL, TRUE));
						$this->native_session->set('msg', (!empty($result['msg'])? $result['msg']: "The teacher data has been updated."));
						redirect(base_url().'profile/teacher_data');
					}
				}
				else $data['msg'] = "WARNING: Your teacher detals could not be resolved for update";
			}
			
			# 2. Show the appropriate message
			if(!empty($result)) $this->native_session->set('msg', (!empty($result['msg'])? $result['msg']: "Your teacher data has been updated."));
			
		}
		
		#If editing - and for the first time, load the id details into the session 
		if(!empty($data['id']) && empty($data['edit']) && empty($_POST)) $this->_teacher->populate_session($data['id']);
		if(!empty($data['action']) && $data['action'] == 'view') $data['preview'] = "Y";
		
		# This helps differentiate source of command for shared functions with the teacher's registration functionality
		$this->native_session->set('is_teacher_updating', 'Y');
		
		$this->load->view('teacher/new_teacher', $data); 
	}
	
	
	
	# Update user profile
	function user_data()
	{
		$data = filter_forwarded_data($this);
		# Log out the user if the session is not available
		if($this->native_session->get('__user_id'))
		{
			if(!empty($_POST))
			{
				if(!empty($_FILES)) $this->load->model('_document');
				
				# Process the signature file if one has been uploaded
				if(!empty($_FILES['signature__fileurl']) && file_exists($_FILES['signature__fileurl']['tmp_name']))
				{
					$upload = $this->_document->upload($_FILES['signature__fileurl'], array('type'=>'image'));
					$_POST['signature__fileurl'] = $upload['file'];
				}
				
				# Process the photo file if one has been uploaded
				if(!empty($_FILES['photo__fileurl']) && file_exists($_FILES['photo__fileurl']['tmp_name']))
				{
					$upload = $this->_document->upload($_FILES['photo__fileurl'], array('type'=>'image'));
					$_POST['photo__fileurl'] = $upload['file'];
				}
				
				$data['result'] = $this->_user->update($this->native_session->get('__user_id'), $this->input->post(NULL, TRUE));
				$data['msg'] = $data['result']['boolean'] && empty($data['result']['msg'])? "Please check your email for a confirmation code to proceed.": $data['result']['msg'];
			}
			
			$this->_user->populate_session($this->native_session->get('__user_id'),true);
			$this->load->view('profile/user_data', $data); 
		}
		else
		{
			redirect(base_url()."account/logout");
		}
	}
	
	
}

/* End of controller file */