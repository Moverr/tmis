<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This class handles running cron jobs for the system.
 *
 * @author Al Zziwa <azziwa@gmail.com>
 * @version 1.1.0
 * @copyright TMIS
 * @created 01/20/2015
 */

class Cron extends CI_Controller 
{
	#Constructor to set some default values at class load
	function __construct()
    {
        parent::__construct();
	}
	
	
	# Update the query cache
	function update_query_cache()
	{
		# DISABLE IF IN DEV TO SEE IMMEDIATE CHANGES IN YOUR QUERIES
		if(ENABLE_QUERY_CACHE) $this->_query_reader->load_queries_into_cache();
	}
	
	
	# Update the message cache
	function update_message_cache()
	{
		# DISABLE IF IN DEV TO SEE IMMEDIATE CHANGES IN YOUR MESSAGES
		if(ENABLE_MESSAGE_CACHE) $this->_messenger->load_messages_into_cache();
	}
	
	
	
	# Fetch and run all system jobs
	public function fetch_and_run_sys_jobs()
	{
		$data = filter_forwarded_data($this);
		$this->load->model('_cron');
		$result = $this->_cron->run_available_jobs();
		
		#Log the results from the run
		if(!empty($data['jobid'])) $this->_cron->update_status($data['jobid'], array(
			'job_type'=>'jobs', 
			'job_code'=>'fetch_and_run_sys_jobs', 
			'result'=>($result['bool']? 'success': 'fail'),
			'job_details'=>$result
		)); 
	}
	
}

/* End of controller file */