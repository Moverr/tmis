<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This class controls acount registration on the system.
 *
 * @author Al Zziwa <azziwa@gmail.com>
 * @version 1.1.0
 * @copyright TMIS
 * @created 01/08/2015
 */

class Register extends CI_Controller 
{
	# Constructor to set some default values at class load
	function __construct()
    {
        parent::__construct();
		$this->load->model('_person');
	}
	
	
	#The first step form for the registration process
	function step_one()
	{
		$data = filter_forwarded_data($this);
		
		# Are you just editing the step for preview?
		if(!empty($data['action']) && $data['action'] == 'edit_preview')
		{
			$this->native_session->set('just_preview_1', 'Y');
		}
		else if(!empty($data['action']) && $data['action'] == 'prefill')
		{
			$this->load->model('_teacher');
			$this->_teacher->populate_session($this->native_session->get('__user_id'));
		}
		
		# The user posted the form
		if(!empty($_POST))
		{
			#Pass these details to the person object to handle with XSS filter turned on
			$data['result'] = $this->_person->add_profile($this->input->post(NULL, TRUE));
			$data['msg'] = $data['result']['boolean'] && empty($data['result']['msg'])? "Please check your email for a confirmation code to proceed.": $data['result']['msg'];
		}
		
		
		# Prepare appropriate message and view to load
		if(!empty($data['result']['boolean']) && $data['result']['boolean'])
		{
			if($this->input->post('justpreview'))
			{
				$data['msg'] = "Your application had been saved. Click Submit to finish.";
				$this->native_session->delete('just_preview_1');
				$viewToLoad = 'register/step_four';
			}
			else
			{
				$viewToLoad = 'register/step_two';
			} 
		}
		else
		{
			$viewToLoad = 'register/step_one';
		}
		
		$this->load->view($viewToLoad, $data); 
	}
	
	
	
	
	
	
	#The second step form for the registration process
	function step_two()
	{
		$data = filter_forwarded_data($this);
		
		# Are you just editing the step for preview?
		if(!empty($data['action']) && $data['action'] == 'edit_preview')
		{
			$this->native_session->set('just_preview_2', 'Y');
		}
		
		# The user posted the form
		if(!empty($_POST))
		{
			if($this->native_session->get('person_id'))
			{
				#Pass these details to the person object to handle with XSS filter turned on
				$data['result'] = $this->_person->add_id_and_contacts($this->native_session->get('person_id'), $this->input->post(NULL, TRUE));
				$data['msg'] = $data['result']['boolean'] && empty($data['result']['msg'])? "Please enter your education and qualifications to proceed.": $data['result']['msg'];
			} 
			else 
			{
				$data['msg'] = "ERROR: We could not verify your data. Your session may have expired. If this problem persists, please contact us.";
			}
		}
		
		# Prepare appropriate message and view to load
		if(!empty($data['result']['boolean']) && $data['result']['boolean'])
		{
			if($this->input->post('justpreview'))
			{
				$data['msg'] = "Your application had been saved. Click Submit to finish.";
				$this->native_session->delete('just_preview_2');
				$viewToLoad = 'register/step_four';
			}
			else if($this->input->post('justsaving'))
			{
				$data['msg'] = "Your application had been saved. <br>You will need to login using the details sent to your email to proceed with your application.";
				$viewToLoad = 'account/login';
			}
			else
			{
				$viewToLoad = 'register/step_three';
			} 
		}
		else
		{
			$viewToLoad = 'register/step_two';
		}
		
		$this->load->view($viewToLoad, $data); 
	}
	
	
	
	
	
	
	# The third step form for the registration process
	function step_three()
	{
		$data = filter_forwarded_data($this);
		
		# Are you just editing the step for preview?
		if(!empty($data['action']) && $data['action'] == 'edit_preview')
		{
			$this->native_session->set('just_preview_3', 'Y');
		}
		
		# The user posted the form
		if(!empty($_POST))
		{
			if($this->native_session->get('person_id'))
			{
				# a) Are they adding an education?
				if(!empty($data['action']) && $data['action'] == 'add_education')
				{
					$data['response'] = $this->_person->add_education($this->native_session->get('person_id'), $this->input->post(NULL, TRUE));
					$data['area'] = "education_list";
					$viewToLoad = "addons/basic_addons";
				}
			
				# b) Are they adding a subject?
				else if(!empty($data['action']) && $data['action'] == 'add_subject')
				{
					$data['response'] = $this->_person->add_subject_taught($this->native_session->get('person_id'), $this->input->post(NULL, TRUE));
					$data['area'] = "subject_list";
					$viewToLoad = "addons/basic_addons";
				}
			
				# c) Are they adding a document?
				else if(!empty($data['action']) && $data['action'] == 'add_document')
				{
					# Process the document addition if one has been successfully uploaded
					if(!empty($_FILES['documenturl__fileurl']) && file_exists($_FILES['documenturl__fileurl']['tmp_name']))
					{
						$this->load->model('_document');
						$upload = $this->_document->upload($_FILES['documenturl__fileurl'], array('type'=>'document'));
						$_POST['documenturl__fileurl'] = $upload['file'];
					}
					$data['response'] = $this->_person->add_document($this->native_session->get('person_id'), $this->input->post(NULL, TRUE));
					
					$data['area'] = "document_list";
					$viewToLoad = "addons/basic_addons";
				}
				
				# d) Are they submitting the entire form to save the data?
				else if($this->native_session->get('education_list') && $this->native_session->get('subject_list'))
				{
					#Pass these details to the person object to handle with XSS filter turned on
					$data['result'] = $this->_person->add_education_and_qualifications($this->native_session->get('person_id'), $this->input->post(NULL, TRUE));
					$data['msg'] = $data['result']['boolean'] && empty($data['result']['msg'])? "Please review your details and submit.": $data['result']['msg'];
				}
				
				# d) Has the user submitted an incomplete form?
				else
				{
					$data['result']['boolean'] = false;
					$data['msg'] = "WARNING: You need to provide both the education and subjects studied to continue.";
				}
			}
			else 
			{
				$data['msg'] = "ERROR: We could not verify your data. Your session may have expired. If this problem persists, please contact us.";
			}
		}
		
		
		if(!empty($data['result']['boolean']) && $data['result']['boolean'])
		{
			if($this->input->post('justpreview'))
			{
				$data['msg'] = "Your application has been saved. Click Submit to finish.";
				$this->native_session->delete('just_preview_3');
				$viewToLoad = 'register/step_four';
			}
			else if($this->input->post('justsaving'))
			{
				$data['msg'] = "Your application had been saved. <br>You will need to login using the details sent to your email to proceed with your application.";
				$viewToLoad = 'account/login';
			}
			else
			{
				$viewToLoad = 'register/step_four';
			} 
		}
		
		
		$viewToLoad = !empty($viewToLoad)? $viewToLoad: 'register/step_three';
		$this->load->view($viewToLoad, $data); 
	}
	
	
	
	
	# Function to edit a registration list item
	function edit_list_item()
	{
		$data = filter_forwarded_data($this);
		
		# Populate the edit form if the necessary data is available
		if(!empty($data['type']) && !empty($data['item_id']) && $this->native_session->get($data['type'].'_list'))
		{
			$this->native_session->set('edit_step_3_'.$data['type'], 'Y');
			$data['details'] = get_row_from_list($this->native_session->get($data['type'].'_list'), $data['type'].'_id', $data['item_id']);
			$data['details']['item_id'] = $data['item_id'];
		}
		# Prepare appropriate message
		$data['msg'] = empty($data['details'])? "ERROR: We could not resolve the item you are attempting to edit.": "";
		
		$data['area'] = !empty($data['type'])? $data['type']."_form": "basic_msg";
		$this->load->view('addons/basic_addons', $data); 
	}
	
	
	
	
	# Function to delete a registration list item
	function delete_list_item()
	{
		$data = filter_forwarded_data($this);
		
		if(!empty($data['type']) && !empty($data['item_id']))
		{
			$result = $this->_person->remove_list_item($data['type'], $data['item_id']);
		}
		
		$data['response']['msg'] = (!empty($result) && $result)? "The ".$data['type']." has been deleted.": "ERROR: There was a problem deleting the ".$data['type'].".";
		$data['area'] = !empty($data['type'])? $data['type']."_list": "basic_msg";
		$this->load->view('addons/basic_addons', $data); 
	}
	
	
	
	
	
	
	#The third step form for the registration process
	function step_four()
	{
		$data = filter_forwarded_data($this);
		
		# The user posted the form
		if(!empty($_POST))
		{
			$result = $this->_person->submit_application($this->native_session->get('person_id'), array('user_id'=>($this->native_session->get('__user_id')? $this->native_session->get('__user_id'): $this->native_session->get('user_id')), 'emailaddress'=>$this->native_session->get('emailaddress'), 'first_name'=>$this->native_session->get('firstname'), 'last_name'=>$this->native_session->get('lastname')));
			if($result['boolean'])
			{
				$data['msg'] = !empty($result['msg'])? $result['msg']: "Your application has been submitted. You will be notified using your registered email when it is approved.<br><br>Please check your email spam folder if you can not find your confirmation email.";
				$viewToLoad = 'account/login';
			}
			else
			{
				$data['msg'] = !empty($result['msg'])? $result['msg']: "ERROR: There was a problem submitting your application. Please try again or contact us if the problem persists.";
				$viewToLoad = 'register/step_four';
			}
			
		}
		
		$viewToLoad = !empty($viewToLoad)? $viewToLoad: 'register/step_four';
		$this->load->view($viewToLoad, $data); 
	}
	
	
}

/* End of controller file */