<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This class controls viewing interview pages on the system.
 *
 * @author Al Zziwa <azziwa@gmail.com>
 * @version 1.1.0
 * @copyright TMIS
 * @created 01/20/2015
 */

class Interview extends CI_Controller 
{
	#Constructor to set some default values at class load
	public function __construct()
    {
        parent::__construct();
		$this->load->model('_interview');
	}
	
	
	#Set interview date
	function set_date()
	{
		$data = filter_forwarded_data($this);
		check_access($this, 'set_interview_date');
		
		if(!empty($data['id']))
		{	
			if(!empty($_POST))
			{
				$data['result'] = $this->_interview->add_new($data['id'], $this->input->post(NULL, TRUE));
				$this->native_session->set('msg', ($data['result']? 'The interview has been set': 'ERROR: We could not set the interview.'));
			}
			else if($data['id'])
			{
				$this->_interview->populate_session($data['id'], 'application');
			}
		}
		else
		{
			$data['msg'] = "ERROR: We can not resolve the application details.";
		}
		
		$data['area'] = "set_date";
		$this->load->view('interview/addons', $data); 
	}
	
	
	
	
	# View an interview list
	function lists()
	{
		$data = filter_forwarded_data($this);
		
		$instructions['action'] = array('shortlist'=>'set_vacancy_shortlist', 'setdate'=>'set_interview_date', 'cancel'=>'cancel_interview', 'recommend'=>'submit_recommendation_for_job', 'recommendations'=>'view_recommendation_list', 'addresult'=>'add_interview_results', 'result'=>'view_interview_results');
		check_access($this, get_access_code($data, $instructions));
		
		# remove the select multi session if it is available
		if($this->native_session->get('__select_multi')) $this->native_session->delete('__select_multi');
		
		$data['list'] = $this->_interview->get_list($data);
		$this->load->view('interview/list_interviews', $data); 
	}
	
	
	
	
	
	# Recommend an applicant
	function recommend()
	{
		$data = filter_forwarded_data($this);
		check_access($this, 'submit_recommendation_for_job');
		
		if(!empty($data['id']))
		{	
			if(!empty($_POST))
			{
				$data['result'] = $this->_interview->add_note($data['id'], $this->input->post('details'), 'recommendation');
				$this->native_session->set('msg', ($data['result']? 'Your recommendation has been submitted': 'ERROR: We could not submit your recommendation.'));
			}
			else if($data['id'])
			{
				$this->_interview->populate_session($data['id'], 'application');
			}
		}
		else
		{
			$data['msg'] = "ERROR: We can not resolve the application details.";
		}
		
		$data['area'] = "submit_recommendation";
		$this->load->view('interview/addons', $data); 
	}
	
	
	
	
	
	
	# View applicant recommendations
	function recommendations()
	{
		$data = filter_forwarded_data($this);
		
		$data['list'] = $this->_interview->get_recommendations($data['id']);
		$data['area'] = "recommendation_list";
		$this->load->view('interview/addons', $data); 
	}
	
	
	
	
	
	# Add a note
	function add_note()
	{
		$data = filter_forwarded_data($this);
		
		if(!empty($data['id']))
		{	
			if(!empty($_POST))
			{
				$data['result'] = $this->_interview->add_note($data['id'], $this->input->post('details'), 'normal');
				$this->native_session->set('msg', ($data['result']? 'Your note has been added': 'ERROR: We could not add your note.'));
			}
			else if($data['id'])
			{
				$this->_interview->populate_session($data['id'], 'interview');
			}
		}
		else
		{
			$data['msg'] = "ERROR: We can not resolve the application details.";
		}
		
		$data['area'] = "add_note";
		$this->load->view('interview/addons', $data); 
	}
	
	
	
	
	
	
	# View interview notes
	function notes()
	{
		$data = filter_forwarded_data($this);
		
		$data['list'] = $this->_interview->get_notes($data['id']);
		$data['area'] = "note_list";
		$this->load->view('interview/addons', $data); 
	}
	
	
	
	
	
	
	# Update an interview result
	function set_result()
	{
		$data = filter_forwarded_data($this);
		
		if(!empty($data['id']))
		{	
			if(!empty($_POST))
			{
				$data['result'] = $this->_interview->set_result($data['id'], $this->input->post(NULL, TRUE)); 
				$this->native_session->set('msg', ($data['result']? 'The interview result has been posted.': 'ERROR: We could not post the interview result.'));
			}
			else if($data['id'])
			{
				$this->_interview->populate_session($data['id'], 'interview');
			}
		}
		else
		{
			$data['msg'] = "ERROR: We can not resolve the application details.";
		}
		
		$data['area'] = "set_result";
		$this->load->view('interview/addons', $data); 
	}
	
	
	
	
	
	# Select interview board
	function select_board()
	{
		$data = filter_forwarded_data($this);
		if(empty($_POST)) $this->_interview->clear_board();
		
		if(!empty($data['id']))
		{	
			if(!empty($_POST))
			{
				$data['result'] = $this->_interview->add_board($data['id'], $this->input->post(NULL, TRUE)); 
				$this->native_session->set('msg', ($data['result']? 'The interview board has been set.': 'ERROR: We could not set the interview board.'));
				redirectFromIframe(base_url().'interview/lists/action/addresult');
				
			}
			else $this->_interview->populate_board($data['id']);
		}
		else
		{
			$data['msg'] = "ERROR: We can not resolve the interview details.";
		}
		
		$data['area'] = "select_board";
		$this->load->view('interview/addons', $data); 
	}
	
	
	
	
	# Add a member to an interview board
	function add_board_member()
	{
		$data = filter_forwarded_data($this);
		
		$data['msg'] = $this->_interview->add_member_to_board($data);
		$data['area'] = "select_board_members";
		$this->load->view('interview/addons', $data); 
	}
	
	
	
	
	# Remove a member from an interview board
	function remove_board_member()
	{
		$data = filter_forwarded_data($this);
		
		$data['msg'] = $this->_interview->remove_member_from_board($data);
		$data['area'] = "select_board_members";
		$this->load->view('interview/addons', $data); 
	}
	
	
	
	
	# View board members
	function view_board_members()
	{
		$data = filter_forwarded_data($this);
		
		$data['msg'] = $this->_interview->populate_board_list($data);
		$data['area'] = "select_board_members";
		$this->load->view('interview/addons', $data); 
	}
	
	
		
	
	# Verify the interview - just keeping the function name consistent
	function verify()
	{
		$data = filter_forwarded_data($this);
		if(!empty($_POST))
		{
			# Cancel and interview
			$result = $this->_interview->verify($_POST);
			$this->native_session->set('msg', ($result['boolean']? "The interview has been cancelled.": "ERROR: The interview could not be cancelled." ));
		}
		else
		{
			# Get list type
			$data['list_type'] = current(explode("_", $data['action']));
			$data['area'] = 'verify_interview';
			$this->load->view('addons/basic_addons', $data);
		}
	}
	
	
	
		
	
	# View the interview shortlist
	function shortlist()
	{
		$data = filter_forwarded_data($this);
	
		if(!empty($data['name']) && !empty($data['vacancy']))
		{
			$data['shortlist_name'] = decrypt_value($data['name']);
			$data['list'] = $this->_interview->get_shortlist($data['vacancy'], $data['shortlist_name']);
		}
		else $data['msg'] = "ERROR: We could not resolve the shortlist details.";
		
		$data['area'] = "view_shortlist";
		$this->load->view('interview/addons', $data); 
	}
	
	
	
	# Download a shortlist
	function download_list()
	{
		$data = filter_forwarded_data($this);
		
		if(!empty($data['name']) && !empty($data['vacancy']))
		{
			$data['shortlist_name'] = decrypt_value($data['name']);
			$data['list'] = $this->_interview->get_shortlist($data['vacancy'], $data['shortlist_name']);
			$data['vacancy_details'] = $this->_interview->get_vacancy_details($data['vacancy']);
			
			$data['area'] = "download_shortlist_csv";
			$this->load->view('page/download', $data); 
		}
		else
		{
			$data['msg'] = "ERROR: The shortlist details can not be resolved.";
			$data['area'] = "view_shortlist";
			$this->load->view('interview/addons', $data); 
		}
	}
	
	
	
	
	# Select more than one user for sending a 
	function select_multi_user()
	{
		$data = filter_forwarded_data($this);
		# Should we show the checkboxes
		if(!empty($data['clear'])) $this->native_session->delete('__select_multi');
		else $this->native_session->set('__select_multi', 'setdate');
		
		if(!empty($_POST))
		{
			# Set interviews if users were selected
			if($this->input->post('interviewuser') && $this->input->post('applicationid'))
				$result = $this->_interview->set_multiuser_interviews($this->input->post(NULL, TRUE));
			else $data['msg'] = "WARNING: No interview candidates were identified.";
			
			# Remove the session variable for the multi-selection when done
			if(!empty($result) && $result)
			{
				$this->native_session->delete('__select_multi');
				$data['msg'] = "The interview dates have been set and the candidates and interviewer notified.";
			}
			else if(empty($data['msg'])) $data['msg'] = "ERROR: We could not set the interviews for the users.";
			
			$data['area'] = "basic_msg";
			$this->load->view('addons/basic_addons', $data);
		}
	}
	
	
	
	
}

/* End of controller file */