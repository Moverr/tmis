<?php
/**
 * This class manages system cron jobs.
 *
 * @author Al Zziwa <azziwa@gmail.com>
 * @version 1.1.0
 * @copyright TMIS
 * @created 01/23/2015
 */
class _cron extends CI_Model
{
	
	#Function to run available cron jobs
	public function run_available_jobs($runtime='', $restrictions=array())
	{
		$runtime = !empty($runtime)? $runtime: date('Y-m-d H:i:s');
		
		#Note the time the cron batch is being updated - hence the start of the new cron job batch run - if any
		$batchMarker = PHP_EOL.PHP_EOL."----------------------------------------------------".PHP_EOL.
						"BATCH TIME: ".$runtime.PHP_EOL.
						"----------------------------------------------------".PHP_EOL;
		file_put_contents(CRON_FILE_LOG, $batchMarker, FILE_APPEND); 
		
		
		$crons = $this->get_crons_to_run($runtime, $restrictions);
		$cronFile = !empty($restrictions['cron_file'])? $restrictions['cron_file']: CRON_FILE;
		
		#Append if there are restrictions, else overwrite the entire file
		if(!empty($restrictions)) file_put_contents($cronFile, $this->generate_job_list_for_crontab($crons, $runtime), FILE_APPEND); else file_put_contents($cronFile, $this->generate_job_list_for_crontab($crons, $runtime));
		
		#Only update the server crontab if you are in the DEFAULT_CRON Installation
		if(CRON_HOME_URL == DEFAULT_CRON_HOME_URL) 
		{
			#Combine the cron job files based on the defined installations
			$cronInstallations = unserialize(CRON_INSTALLATIONS);
			foreach($cronInstallations AS $i=>$fileLocation)
			{
				$cronFileContents = file_get_contents($fileLocation.CRON_FILE_NAME);
				if($i > 0) file_put_contents(GLOBAL_CRON_FILE, $cronFileContents, FILE_APPEND); else file_put_contents(GLOBAL_CRON_FILE, $cronFileContents);
			}
			
			$runResult = shell_exec('sudo -u ubuntu -S crontab '.GLOBAL_CRON_FILE);
		}
		
		return array('bool'=>TRUE, 'total'=>count($crons), 'runtime'=>$runtime); 
	}
	
	
	
	private function generate_job_list_for_crontab($cronjobs, $runtime)
	{
		$cronString="";
		#Fill the cron file with the jobs to run
		foreach($cronjobs AS $job)
		{
			$cronString .= $this->get_time_placements($job, $runtime).' '.PHP_LOCATION.' '.CRON_HOME_URL.'index.php cron/'.$job['job_type'].' '.$job['run_url'].' >> '.CRON_FILE_LOG.PHP_EOL;
		}
		
		return $cronString.PHP_EOL;
	}
	
	
	
	
	
	# Get cron jobs to run at the passed time
	public function get_crons_to_run($runtime='', $restrictions=array())
	{
		$readyCrons = array();
		
		# Get the cron list
		$cronList = $this->_query_reader->get_list('get_cron_schedules', array('is_done'=>'N', 'extra_conditions'=>'', 'limit_text'=>''));
		
		# Format query ready for running
		foreach($cronList AS $key=>$cron) 
		{
			$readyCrons[$key] = $cron;
			$readyCrons[$key]['run_url'] = $cron['activity_code'].(!empty($cron['cron_value'])? '/'.str_replace(',', '/', str_replace('=', '/', $cron['cron_value'])) :'')."/jobid/".$cron['id']; 
		}
		
		return $readyCrons;
	}
	
	
	
	
	# Format cron query
	private function format_cron_query($cronCodes, $runtime)
	{
		$queryArray = array();
		
		foreach($cronCodes AS $code)
		{
			switch($code)
			{
				case 'never':
					array_push($queryArray, " (DATE('".date('Y-m-d H:i:s', strtotime($runtime))."') >= run_time AND repeat_code='never') "); 
				break;
				
				case 'every_half_hour':
					array_push($queryArray, " ('".date('i:s', strtotime($runtime))."'='30:00' OR '".date('i:s', strtotime($runtime))."'='00:00' AND repeat_code='every_half_hour') ");
				break;
				
				case 'every_hour':
					array_push($queryArray, " ('".date('i:s', strtotime($runtime))."'='00:00' AND repeat_code='every_hour') ");
				break;
				
				case 'end_of_day':
					array_push($queryArray, " ('".date('H:i:s', strtotime($runtime))."'='00:00:00' AND repeat_code='end_of_day') ");
				break;
				
				case 'end_of_week':
					$week =  date('W', strtotime('now'));
   					$year =  date('Y', strtotime('now'));
					array_push($queryArray, " ('".date("Y-m-d H:i:s", strtotime($runtime))."'='".date("Y-m-d H:i:s", strtotime("{$year}-W{$week}-7"))."' AND repeat_code='end_of_week') ");
				break;
				
				case 'end_of_month':
					array_push($queryArray, " ('".date("Y-m-d H:i:s", strtotime($runtime))."'='".date("Y-m-t H:i:s", strtotime('now'))."' AND repeat_code='end_of_month') ");
				break;
				
				case 'default':
					array_push($queryArray, " repeat_code='default' ");
				break;
				
				default:
				break;
			}
		}
		
		return !empty($queryArray)? implode(' OR ', $queryArray): '';
	}
	
	
	
	
	#Get the time format for passing to the queries
	public function get_time_placements($job, $runtime)
	{
		$time = "* * * * * ";
		
		switch($job['repeat_code'])
		{
				case 'never':
					$timestamp = strtotime($runtime);
					$time = (date('i',$timestamp)+0)." ".date('G',$timestamp)." ".date('j',$timestamp)." ".date('n',$timestamp)." ".date('w',$timestamp);
				break;
				
				case 'every_half_hour':
					$time = "30 * * * * ";
				break;
				
				case 'every_hour':
					$time = "0 * * * * ";
				break;
				
				case 'end_of_day':
					$time = "0 0 * * * ";
				break;
				
				case 'end_of_week':
					$time = "0 0 * * 0 ";
				break;
				
				case 'end_of_month':
					$time = "0 0 * ".date("t", strtotime($runtime))." * ";
				break;
				
				case 'default':
					$timeParts = explode(' ', CRON_REFRESH_PERIOD);
					
					$time = (strpos($timeParts[1], 'minute') !== FALSE? '*/'.$timeParts[0]: '*')." ".
					(strpos($timeParts[1], 'hour') !== FALSE? '*/'.$timeParts[0]: '*')." ".
					(strpos($timeParts[1], 'day of month') !== FALSE? '*/'.preg_replace("/[^0-9]/","",$timeParts[0]): '*').
					" * ". #Run every year :-) - Not configurable [for now]
					(strpos($timeParts[1], 'day of week') !== FALSE? '*/'.preg_replace("/[^0-9]/","",$timeParts[0]): '*');
				break;
				
				default:
				break;
		}
		
		return $time;
	}
	
	
	
	
	
	
	# STUB: Run the initial user crons
	public function run_initial_user_crons($emailAddress)
	{
		/*$user  = $this->_query_reader->get_row_as_array('get_user_list', array('search_string'=>" AND email_address='".$emailAddress."' ", 'limittext'=>" LIMIT 0,1; "));
		
		if(!empty($user['userId']))
		{
			$result = $this->run_available_crons('', array('user_id'=>$user['userId']));
		}*/
	}
	
	
	
	
	# STUB: Update the status of a cron job after it has been run
	public function update_run_status($jobId, $jobDetails)
	{
		/*$cron = $this->db->query($this->query_reader->get_query_by_code('get_cron_schedules', array('is_done'=>'N', 'extra_conditions'=>" AND id='".$jobId."' ", 'limit_text'=>' LIMIT 0,1; ')))->row_array();
		
		#If the repeat code is never, mark the cron as done
		if(!empty($cron['repeat_code']) && $cron['repeat_code'] == 'never' && $jobDetails['result'] == 'success') $this->db->query($this->query_reader->get_query_by_code('update_cron_schedule_field', array('field_name'=>"is_done", 'field_value'=>"Y", 'id'=>$jobId)));
		$this->db->query($this->query_reader->get_query_by_code('update_cron_schedule_field', array('field_name'=>"when_ran", 'field_value'=>date('Y-m-d H:i:s'), 'id'=>$jobId)));
		$this->db->query($this->query_reader->get_query_by_code('update_cron_schedule_field', array('field_name'=>"last_result", 'field_value'=>$jobDetails['result'], 'id'=>$jobId)));
		
		#Log details of the cron job run
		$this->log_cron_job_results($jobDetails);*/
	}
	
	
	
	
	
	
	
	# STUB: Log cron job results
	# WARNING: This function may not be necessary - with _logger model in place
	public function log_cron_job_results($jobDetails)
	{
		/*return  $this->db->query($this->query_reader->get_query_by_code('add_cron_log', array(
			'user_id'=>(!empty($jobDetails['user_id'])? $jobDetails['user_id']: ''), 
			'job_type'=>(!empty($jobDetails['job_type'])? $jobDetails['job_type']: 'system'), 
			'activity_code'=>$jobDetails['job_code'], 
			'result'=>strtoupper($jobDetails['result']),  
			'uri'=>current_url(), 
			'log_details'=>strtoupper($jobDetails['result']).": ".(is_array($jobDetails['job_details'])? implode(', ', $jobDetails['job_details']): $jobDetails['job_details']), 
			'ip_address'=>get_ip_address())
		));*/
		
	}



}


?>