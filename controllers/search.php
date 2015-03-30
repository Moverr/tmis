<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This class controls generic searching on the website.
 *
 * @author Al Zziwa <azziwa@gmail.com>
 * @version 1.1.0
 * @copyright TMIS
 * @created 01/26/2015
 */

class Search extends CI_Controller 
{
	#Constructor to set some default values at class load
	public function __construct()
    {
        parent::__construct();
		$this->load->model('_finder');
	}
		
	
	# Load a search list
	function load_list()
	{
		$data = filter_forwarded_data($this);
		
		if(!empty($data['type']))
		{
			$data = $this->_finder->load_list($data);
			$this->load->view($data['type'].'/list', $data);
		}
		else
		{
			$data['msg'] = "ERROR: Basic search functionality for this list is not yet added.";
			$data['area'] = "basic_msg";
			$this->load->view('addons/basic_addons', $data);
		}
	}
	
	
	
}

/* End of controller file */