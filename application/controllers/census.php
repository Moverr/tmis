<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This class controls viewing census submissions on the system.
 *
 * @author Al Zziwa <azziwa@gmail.com>
 * @version 1.1.0
 * @copyright TMIS
 * @created 01/30/2015
 */

class Census extends CI_Controller
{
	#Constructor to set some default values at class load
	public function __construct()
    {
        parent::__construct();
		$this->load->model('_census');
	}




	# Add a new census
	function add()
	{
		$data = filter_forwarded_data($this);
		check_access($this, 'submit_teacher_census_data');
		# Remove any session variables if still in the session.
		if(empty($_POST)) $this->_census->clear_session();
				
		# If user has posted the form for processing
		if(!empty($_POST))
		{
			#echo "<BR><BR>POST: "; print_r($_POST);
			$data['result'] = $this->input->post('censusid')? $this->_census->update($this->input->post('censusid'), $this->input->post(NULL, TRUE)): $this->_census->add_new($this->input->post(NULL, TRUE));

			if($this->input->post('censusid')) $data['censusid'] = $this->input->post('censusid');
			#echo "<BR><BR>RESULT: ";print_r($data['result']);
			$data['msg'] = $data['result']['boolean'] && empty($data['result']['msg'])? "The census details have been submitted for approval.": $data['result']['msg'];
			$this->_census->clear_session();
			#echo "MESSAGE: ".$data['msg'];
			# Redirect to appropriate page if successful
			$this->native_session->set('msg', $data['msg']);

			if($data['result']['boolean'] && !$this->input->post('censusid'))
			{
				if(check_access($this, 'view_teacher_census_report', 'boolean', false) || check_access($this, 'verify_teacher_census_submissions', 'boolean', false))
				{
					$action = check_access($this, 'verify_teacher_census_submissions', 'boolean', false)? 'verify': 'view';
					redirect(base_url().'census/lists/action/'.$action);
				}
				else redirect(base_url().get_user_dashboard($this, $this->native_session->get('__user_id') ));
			}
			else if($this->input->post('censusid') && $this->input->post('forward'))
			{
				$data['forward'] = base_url().$this->input->post('forward');
			}
		}

		#If editing, load the id details into the session for the first time
		if(!empty($data['id']) && empty($_POST)) $this->_census->populate_session($data['id']);
		
		$data['responsibility_list'] = $this->_census->get_list(array('action'=>'responsibility'));
		#print_r($data['responsibility_list']); exit();
		$data['training_list'] = $this->_census->get_list(array('action'=>'training'));
		$this->load->view('census/new_census', $data);
	}





	# View a census list
	function lists()
	{
		$data = filter_forwarded_data($this);
		if(empty($data['action'])) $data['action'] = 'view';
		$instructions['action'] = array('view'=>'view_teacher_census_report', 'verify'=>'verify_teacher_census_submissions');
		check_access($this, get_access_code($data, $instructions));

		$data['list'] = $this->_census->get_list($data);
		$this->load->view('census/list_census', $data);
	}





	# View a census sub-list
	function sub_lists()
	{
		$data = filter_forwarded_data($this);

		if(!empty($data['id']) && !empty($data['type'])) $data['list'] = $this->_census->get_list(array('action'=>$data['type'].'_sub_list', 'census_id'=>$data['id']));
		else $data['msg'] = "ERROR: The list instructions can not be resolved.";

		$data['area'] = 'census_sub_lists';
		$this->load->view('addons/basic_addons', $data);
	}



	#Verify the census before proceeding to the next stage
	function verify()
	{
		$data = filter_forwarded_data($this);
		if(!empty($_POST))
		{
			# Approve or reject a census
			$result = $this->_census->verify($_POST);

			$actionPart = current(explode("_", $_POST['action']));
			$actions = array('approve'=>'approved', 'reject'=>'rejected', 'archive'=>'archived', 'restore'=>'restored');
			$actionWord = !empty($actions[$actionPart])? $actions[$actionPart]: 'made';
			$this->native_session->set('msg', ($result['boolean']? "The census has been ".$actionWord: (!empty($result['msg'])? $result['msg']: "ERROR: The census could not be ".$actionWord) ));
		}
		else
		{
			# Get list type
			$data['list_type'] = current(explode("_", $data['action']));
			$data['area'] = 'verify_census';
			$this->load->view('addons/basic_addons', $data);
		}
	}








}

/* End of controller file */
