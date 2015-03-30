<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This class controls viewing promotion pages on the system.
 *
 * @author Al Zziwa <azziwa@gmail.com>
 * @version 1.1.0
 * @copyright TMIS
 * @created 02/08/2015
 */

class Promotion extends CI_Controller 
{
	#Constructor to set some default values at class load
	public function __construct()
    {
        parent::__construct();
		$this->load->model('_promotion');
	}
	
	
	# View promotion application list
	function lists()
	{
		$data = filter_forwarded_data($this);
		if(empty($data['action'])) $data['action'] = 'view';
		$instructions['action'] = array('view'=>'view_promotion_applications', 'report'=>'view_promotions');
		check_access($this, get_access_code($data, $instructions));
		
		
		$this->load->view('page/under_construction', $data); 
	}
	
	
	
	# Cancel a promotion application
	function cancel()
	{
		$data = filter_forwarded_data($this);
		check_access($this, 'cancel_promotion_application');
		$this->load->model('_job');
		
		$data['area'] = 'cancel_promotion_application';
		$data['job'] = $this->_job->populate_session();
		$this->load->view('job/my_profile', $data); 
	}
	
	
	
	
	# Apply for promotion
	function apply()
	{
		$data = filter_forwarded_data($this);
		check_access($this, 'apply_to_retire');
		
		if(!empty($_POST)) 
		{
			$result = $this->_promotion->submit_application($this->input->post(NULL, TRUE));
			$data['msg'] = $result['boolean']? 'Your promotion has been submitted.': $result['msg'];
			if($result['boolean']) 
			{
				$this->native_session->set('msg', $data['msg']);
				redirect(base_url().'promotion/cancel');
			}
		}
		
		$data['current_application'] = $this->_promotion->details($this->native_session->get('__user_id'));
		$this->load->view('promotion/apply', $data); 
	}
	
	
}

/* End of controller file */