<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This class controls viewing approvals on the system.
 *
 * @author Al Zziwa <azziwa@gmail.com>
 * @version 1.1.0
 * @copyright TMIS
 * @created 02/28/2015
 */

class Approval extends CI_Controller 
{
	#Constructor to set some default values at class load
	public function __construct()
    {
        parent::__construct();
		$this->load->model('_approval_chain');
	}
	
	
	# View a approval list
	function lists()
	{
		$data = filter_forwarded_data($this);
		$instructions['action'] = array('report'=>'view_approval_chain');
		check_access($this, get_access_code($data, $instructions));
		
		$data['list'] = $this->_approval_chain->get_list($data);
		$this->load->view('approval/list_approvals', $data); 
	}
	
	
	
}

/* End of controller file */