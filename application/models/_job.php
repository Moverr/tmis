<?php
/**
 * This class manages job data in the system.
 *
 * @author Al Zziwa <azziwa@gmail.com>
 * @version 1.1.0
 * @copyright TMIS
 * @created 01/08/2015
 */
class _job extends CI_Model
{
	
	# Get list of jobs (approved vacancies)
	function get_list($instructions=array())
	{
		$searchString = " V.status='published' ";
		$queryCode = "get_vacancy_list_data";
		$orderBy = " V.date_added DESC ";
		
		if(!empty($instructions['action']) && $instructions['action']== 'saved')
		{
			$searchString = " V.id IN (SELECT vacancy_id FROM saved_vacancies WHERE user_id='".$this->native_session->get('__user_id')."' ORDER BY date_added DESC) ";
		}
		else if(!empty($instructions['action']) && $instructions['action']== 'apply')
		{
			$searchString .= " AND V.id NOT IN (SELECT vacancy_id FROM application WHERE user_id='".$this->native_session->get('__user_id')."') ";
		}
		else if(!empty($instructions['action']) && $instructions['action']== 'status')
		{
			$queryCode = "get_job_applications";
			$orderBy = " A.date_added DESC ";
			$searchString = " A.user_id='".$this->native_session->get('__user_id')."' ";
		}
		else if(!empty($instructions['action']) && in_array($instructions['action'], array('report', 'download')))
		{
			$queryCode = "get_job_applications";
			$orderBy = " A.date_added DESC ";
			$searchString = " 1=1 ";
		}
		
		# If a search phrase is sent in the instructions
		if(!empty($instructions['searchstring']))
		{
			$searchString .= " AND ".$instructions['searchstring'];
		}
		
		# Instructions
		$count = !empty($instructions['pagecount'])? $instructions['pagecount']: NUM_OF_ROWS_PER_PAGE;
		$start = !empty($instructions['page'])? ($instructions['page']-1)*$count: 0;
		
		return $this->_query_reader->get_list($queryCode,array('search_query'=>$searchString, 'viewed_by'=>$this->native_session->get('__user_id'), 'limit_text'=>$start.','.($count+1), 'order_by'=>$orderBy));
	}
	
	
		
		
	
	# Apply for a job
	function apply($vacancyId)
	{
		# 1. Add job application (if it does not exist)
		$result1 = $this->_query_reader->run('add_job_application', array('vacancy_id'=>$vacancyId, 'user_id'=>$this->native_session->get('__user_id'), 'added_by'=>$this->native_session->get('__user_id'), 'status'=>'submitted' ));
		
		# 2. Notify the relevant parties about the application
		$vacancy = $this->_query_reader->get_row_as_array('get_vacancy_by_id', array('vacancy_id'=>$vacancyId));
		
		$result2 = $result1? $this->_messenger->send($this->get_job_parties($vacancyId), array('code'=>'job_application_sent', 'action_date'=>date('d-M-Y', strtotime('now')), 'job_title'=>$vacancy['topic'], 'job_role'=>$vacancy['role_name'], 'institution'=>$vacancy['institution_name'], 'job_summary'=>$vacancy['summary'])): false;
		
		# Return relevant result
		return get_decision(array($result1, $result2));
	}
	
	
	# Get the parties to be notified about a job
	function get_job_parties($vacancyId)
	{
		# The person who posted the job, the active HR users and the applicant them selves
		return $this->_query_reader->get_single_column_as_array('get_job_parties', 'user_id', array('vacancy_id'=>$vacancyId, 'applicant'=>$this->native_session->get('__user_id') ));
	}
	
	
	
	# Verify a job
	function verify($instructions)
	{
		$result = array('boolean'=>false, 'msg'=>'ERROR: The job instructions could not be resolved.');
		
		if(!empty($instructions['action']))
		{
			switch($instructions['action'])
			{
				case 'save':
					$result['boolean'] = $this->save($instructions['id'], 'add');
				break;
				
				case 'archive':
					$result['boolean'] = $this->save($instructions['id'], 'remove');
				break;
				
			}
			
			if(!empty($result['boolean'])) 
			{
				$result['msg'] = $result['boolean']? "The job status has been changed": "ERROR: The job status could not be changed.";
			}
		}
		
		return $result;
	}
	
	
	
	
	#Save a job for the user 
	function save($jobId, $action)
	{
		return $this->_query_reader->run(($action == 'add'? 'save_user_job': 'remove_user_job'), array('vacancy_id'=>$jobId, 'user_id'=>$this->native_session->get('__user_id') ));
	}
	
	
	# Get saved job ids for this user
	function get_saved_jobs()
	{
		return $this->_query_reader->get_single_column_as_array('get_saved_jobs', 'vacancy_id', array('user_id'=>$this->native_session->get('__user_id') ));
	}
	
	
	
	
	# View a teacher's previous jobs
	function previous_jobs()
	{
		return $this->_query_reader->get_list('get_teacher_jobs', array('user_id'=>$this->native_session->get('__user_id'), 'search_condition'=>" AND P.posting_end_date <> '0000-00-00' " ));
	}
	
	
	
	
	# A new teacher submits their current job for approval
	function submit_current_job($details)
	{
		
		$postingId = $this->_query_reader->add_data('add_old_posting', array('postee_id'=>$this->native_session->get('__user_id'),'institution_id'=>$details['schoolid'], 'start_date'=>date('Y-m-d', strtotime($details['startdate'])), 'notes'=>'-- INITIAL SELF POSTING -- This job posting was submitted by the teacher as the initial posting request.', 'job_name'=>htmlentities($details['jobname__jobroles'], ENT_QUOTES), 'status'=>'saved', 'final_interview_id'=>'', 'applied_for_confirmation'=>'Y', 'added_by'=>$this->native_session->get('__user_id') ));
			
		if(!empty($postingId))
		{
			$this->load->model('_approval_chain');
			$result = $this->_approval_chain->add_chain($postingId, 'confirmation', '1', 'approved', '-- INITIAL SELF POSTING --');
			
			$approvers = $this->_messenger->get_users_in_role(array('moes','cao'), $this->native_session->get('__user_id'));
			
			$result = $this->_messenger->send($approvers, array('code'=>'request_teacher_posting', 'applicant_name'=>$this->native_session->get('__full_name'), 'job_name'=>htmlentities($details['jobname__jobroles'], ENT_QUOTES), 'action_date'=>date('d-M-Y h:ia T', strtotime('now')), 'made_by'=>$this->native_session->get('__full_name')));
		}
		
		return !empty($result)? $result: false;
	}
	
	
	
	
	# Populate the job session for display on the user's page
	function populate_session()
	{
		#Get the current user's job desription
		$posting = $this->_query_reader->get_row_as_array('get_job_postings', array('search_query'=>" P.postee_id='".$this->native_session->get('__user_id')."' AND (P.posting_end_date='0000-00-00' OR P.posting_end_date >= NOW()) ", 'limit_text'=>'1', 'order_by'=>' P.last_updated DESC '));
		
		if(!empty($posting))
		{
			$this->native_session->set('postingid', $posting['id']);
			$this->native_session->set('jobname', $posting['job']);
			$this->native_session->set('school', $posting['institution_name']);
			$this->native_session->set('jobdescription', $posting['job_description']);
			$this->native_session->set('startdate', $posting['start_date']);	
			$this->native_session->set('hasapplied', $posting['applied_for_confirmation']);
		}
	}
	
	
	# Request job confirmation
	function request_confirmation($postingId)
	{
		$this->load->model('_approval_chain');
		$result1 = $this->_query_reader->run('submit_confirmation_request', array('posting_id'=>$postingId));
		$result = $this->_approval_chain->add_chain($postingId, 'confirmation', '1', 'approved');
		return get_decision(array($result1, $result['boolean']));
	}
	
	
	
	# Submit a promotion application
	function submit_promotion_application($details)
	{
		$required = array('vacancyid', 'proposeddate');
		$passed = process_fields($this, $details, $required, array("-"));
		
		if($passed['boolean'])
		{
			$result1 = $this->_query_reader->run('add_promotion_application', array('vacancy_id'=>$details['vacancyid'], 'applicant_id'=>$this->native_session->get('__user_id'), 'reason'=>htmlentities($details['promotionreason'], ENT_QUOTES), 'proposed_promotion_date'=>date('Y-m-d', strtotime($details['proposeddate'])), 'added_by'=>$this->native_session->get('__user_id') ));
		
			$result2 = $this->apply($details['vacancyid']);
			$result = get_decision(array($result1, $result2));
			$msg = $result? 'Your promotion application has been submitted.': 'ERROR: We could not submit your promotion application.';
		}
		else
		{
			$msg = empty($details['vacancyid'])? 'ERROR: There is no valid vacancy selected.': 'ERROR: Please enter all required items.';
			$result = false;
		}
		
		return array('boolean'=>$result, 'msg'=>$msg);
	}
	
	
}


?>