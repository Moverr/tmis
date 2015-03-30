<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This class controls viewing reports on the system.
 *
 * @author Al Zziwa <azziwa@gmail.com>
 * @version 1.1.0
 * @copyright TMIS
 * @created 01/20/2015
 */

class Report extends CI_Controller 
{
	# Constructor to set some default values at class load
	public function __construct()
    {
        parent::__construct();
		$this->load->model('_report');
	}
	
	
	# View system-specific lists e.g., log data
	function lists()
	{
		$data = filter_forwarded_data($this);
		$instructions['action'] = array('user'=>'view_user_log', 'system'=>'view_system_log');
		check_access($this, get_access_code($data, $instructions));
		
		$data['list'] = $this->_report->get_list($data);
		$this->load->view('report/list_reports', $data); 
	}
	
	
	
	
	
	
}

/* End of controller file */