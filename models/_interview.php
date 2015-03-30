<?php
/**
 * This class manages interview data.
 *
 * @author Al Zziwa <azziwa@gmail.com>
 * @version 1.1.0
 * @copyright TMIS
 * @created 01/08/2015
 */
class _interview extends CI_Model
{
	
	# Add a new interview
	function add_new($applicationId, $details)
	{
		$result1 = $this->_query_reader->run('add_interview', array('application_id'=>$applicationId, 'interviewer_id'=>$details['userid'], 'planned_date'=>date('Y-m-d H:i:s', strtotime($details['interviewdate'])), 'notes'=>htmlentities($details['notes'], ENT_QUOTES), 'added_by'=>$this->native_session->get('__user_id')));
		
		if($result1)
		{
			$application = $this->_query_reader->get_row_as_array('get_simple_application_details', array('application_id'=>$applicationId));
			
			#Send both the applicant and interviewer notice of interview set
			# Applicant
			$result2_1 = $this->_messenger->send($application['user_id'], array('code'=>'notice_of_set_interview', 'applicant'=>$this->native_session->get('applicant'), 'submission_date'=>date('d-M-Y h:ia T', strtotime($this->native_session->get('submission_date'))), 'institution_name'=>$this->native_session->get('institution_name'), 'interview_notes'=>htmlentities($details['notes'], ENT_QUOTES), 'planned_date'=>$details['interviewdate'], 'interviewer'=>$details['interviewer__users'] ));
			
			#Interviewer
			$result2_2 = $this->_messenger->send($details['userid'], array('code'=>'notify_interviewer_of_date', 'applicant'=>$this->native_session->get('applicant'), 'submission_date'=>date('d-M-Y h:ia T', strtotime($this->native_session->get('submission_date'))), 'institution_name'=>$this->native_session->get('institution_name'), 'interview_notes'=>htmlentities($details['notes'], ENT_QUOTES), 'planned_date'=>$details['interviewdate'], 'interviewer'=>$details['interviewer__users'] ));
			
			$result2 = get_decision(array($result2_1, $result2_2));
		} 
		else $result2 = false;
		
		
		return get_decision(array($result1, $result2));
	}
		
		
		
	# Add a new note
	function add_note($itemId, $note, $noteType='normal')
	{
		# A recommendation
		if($noteType =='recommendation')
		{
			return $this->_query_reader->run('add_recommendation', array('change_application_id'=>$itemId, 'application_type'=>'job', 'recommended_by'=>$this->native_session->get('__user_id'), 'notes'=>htmlentities($note, ENT_QUOTES), 'added_by'=>$this->native_session->get('__user_id') ));
		}
		# A normal note
		else if($noteType =='normal')
		{
			return $this->_query_reader->run('add_note', array('parent_id'=>$itemId, 'parent_type'=>'interview', 'added_by'=>$this->native_session->get('__user_id'), 'details'=>htmlentities($note, ENT_QUOTES) ));
		}
	}	
		
		
		
	# Get the details about a vacancy
	function get_vacancy_details($vacancyId)
	{
		return $this->_query_reader->get_row_as_array('get_vacancy_by_id', array('vacancy_id'=>$vacancyId));
	}	
		
		
		
	# Get the list of applicants in a shortlist
	function get_shortlist($vacancyId, $shortlistName)
	{
		return $this->_query_reader->get_list('get_shortlist_details', array('vacancy_id'=>$vacancyId, 'shortlist_name'=>$shortlistName));
	}		
		
		
		
	# Set the interview result
	function set_result($interviewId, $details)
	{
		# 1. Update the interview details with the result
		$result1 = $this->_query_reader->run('update_interview_data', array('interview_id'=>$interviewId, 'updated_by'=>$this->native_session->get('__user_id'), 'interview_date'=>date('Y-m-d H:i:s', strtotime($details['interviewdate'])), 'interview_duration'=>$details['duration'], 'result'=>strtolower($details['result__interviewresults']))); 
		
		
		# 2. If shortlist is given then update the shortlist record to include the candidate
		if(strtolower($details['result__interviewresults']) == 'passed' && !empty($details['shortlist__shortlists']))
		{
			$result2 = $this->_query_reader->run('add_user_to_shortlist', array('shortlist_name'=>$details['shortlist__shortlists'], 'added_by'=>$this->native_session->get('__user_id'), 'vacancy_id'=>$details['jobid'], 'applicant_id'=>$this->native_session->get('applicant_id') ));
		}
		# If the job has been awarded, notify moes/cao user to post the teacher
		else if(strtolower($details['result__interviewresults']) == 'awarded')
		{
			#Save a temporary posting for the approvers to review before approving
			$result2 = $this->_query_reader->run('add_posting_data', array('postee_id'=>$this->native_session->get('applicant_id'),'notes'=>(!empty($details['notes'])? htmlentities($details['notes'], ENT_QUOTES): 'NONE'), 'final_interview_id'=>$interviewId, 'vacancy_id'=>$details['jobid'], 'status'=>'saved', 'added_by'=>$this->native_session->get('__user_id')));
			
			if($result2)
			{
				$approvers = $this->_messenger->get_users_in_role(array('moes','cao'), $this->native_session->get('applicant_id'));
			
				$result = $this->_messenger->send($approvers, array('code'=>'request_teacher_posting', 'applicant_name'=>$this->native_session->get('applicant'), 'job_name'=>$this->native_session->get('job'), 'action_date'=>date('d-M-Y h:ia T', strtotime('now')), 'made_by'=>$this->native_session->get('__full_name')));
			}
			
		}
		else $result2 = true;
		
		
		# 3. Record a note if any is given
		if(!empty($details['notes']))
		{
			$result3 = $this->_query_reader->run('add_note', array('parent_id'=>$interviewId, 'parent_type'=>'interview', 'added_by'=>$this->native_session->get('__user_id'), 'details'=>htmlentities("-- The Candidate ".strtoupper($this->native_session->get('applicant'))." has ".strtoupper($details['result__interviewresults'])." because: --<br><br>".$details['notes'], ENT_QUOTES) )); 
		}
		else $result3 = true;
		
		# 4. Notify the candidate about the result
		if($result1 && $result2 && $result3) $result4 = $this->_messenger->send($this->native_session->get('applicant_id'), array('code'=>'notify_interview_status_change', 'interview_result'=>strtoupper($details['result__interviewresults']), 'action_date'=>date('d-M-Y h:ia T'), 'made_by'=>$this->native_session->get('__full_name'), 'applicant_name'=>$this->native_session->get('applicant'), 'job_name'=>$this->native_session->get('job'), 'interview_date'=>date('d-M-Y h:ia T', strtotime($details['interviewdate'])), 'interview_notes'=>(!empty($details['notes'])? $details['notes']: 'NONE') ));
		else $result4 = false;
		
		return get_decision(array($result1, $result2, $result3, $result4), FALSE);
	}	
		
		
	
		
	
	# Get list of interviews
	function get_list($instructions=array())
	{
		$searchString = " 1=1 ";
		$queryCode = 'get_interview_vacancies';
		$orderBy = ' I2.last_updated DESC ';
		
		if(!empty($instructions['action']) && $instructions['action'] == 'shortlist')
		{
			$queryCode = 'get_interview_shortlists';
			$orderBy = ' V.topic ASC ';
			# Narrow the items viewed for the teacher to their items only
			if($this->native_session->get('__permission_group') == '2') $searchString .= " AND S.applicant_id='".$this->native_session->get('__user_id')."' ";
		}
		else if(!empty($instructions['action']) && in_array($instructions['action'], array('setdate', 'recommend', 'recommendations')) )
		{
			$queryCode = 'get_job_applications';
			$orderBy = ' A.date_added DESC ';
			# Narrow the items viewed for the teacher to their items only
			if($this->native_session->get('__permission_group') == '2') $searchString .= " AND A.user_id='".$this->native_session->get('__user_id')."' ";
			
		}
		else
		{
			# Narrow the items viewed for the teacher to their items only
			if($this->native_session->get('__permission_group') == '2') $searchString .= " AND A.user_id='".$this->native_session->get('__user_id')."' ";
		}
		
		#Filter out un-needed results by status (I2 = interview table alias)
		if(!empty($instructions['action']) && $instructions['action'] == 'addresult')
		{
			$searchString .= " AND I2.result <> 'awarded' ";
		}
		if(!empty($instructions['action']) && $instructions['action'] == 'cancel')
		{
			$searchString .= " AND I2.result = 'pending' ";
		}
		
		
		# Narrow the items viewed for the manager to their school(s) only
		if($this->native_session->get('__permission_group') == '3')
		{
			if($queryCode == 'get_interview_shortlists') $institutionField = "I.id";
			else if($queryCode == 'get_job_applications') $institutionField = "PS.institution_id";
			else $institutionField = "I.id";
			
			$searchString .= " AND ".$institutionField." IN ('".implode("','", $this->get_postings($this->native_session->get('__user_id')))."') ";
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

	
	
		
	

	# Populate a school session profile
	function populate_session($itemId, $itemType='interview')
	{
		if($itemType == 'application')
		{
			$item = $this->_query_reader->get_row_as_array('get_job_applications', array('search_query'=>" A.id='".$itemId."' ", 'limit_text'=>'1', 'viewed_by'=>$this->native_session->get('__user_id'), 'order_by'=>' A.date_added DESC '));
		
			$this->native_session->set('applicant', $item['applicant_name']);
			$this->native_session->set('submission_date', $item['submission_date']);
			$this->native_session->set('institution_name', $item['institution_name']);
		}
		
		else if($itemType == 'interview')
		{
			$item = $this->_query_reader->get_row_as_array('get_interview_vacancies', array('search_query'=>" I2.id='".$itemId."' ", 'limit_text'=>'1', 'order_by'=>' I2.date_added DESC '));
		
			$this->native_session->set('job', $item['job']);
			$this->native_session->set('job_id', $item['job_id']);
			$this->native_session->set('applicant', $item['applicant']);
			$this->native_session->set('applicant_id', $item['applicant_id']);
			$this->native_session->set('interviewer', $item['interviewer']);
			$this->native_session->set('interview_date', $item['interview_date']);
		}
	}
	
	
	
	# Get the recommendations for an application
	function get_recommendations($applicationId)
	{
		return $this->_query_reader->get_list('get_application_recommendations', array('application_id'=>$applicationId, 'application_type'=>'job'));
	}
	
	
	
	# Get the notes on an interview
	function get_notes($interviewId)
	{
		return $this->_query_reader->get_list('get_item_notes', array('item_id'=>$interviewId, 'item_type'=>'interview'));
	}
	
	
	#Get the postings of a user given their ID 
	function get_postings($userId)
	{
		return $this->_query_reader->get_single_column_as_array('get_user_posting', 'institution_id', array('user_id'=>$userId));
	}
	
	
	
	
	
	
	
	
	# Verify an interview
	function verify($instructions)
	{
		$result = array('boolean'=>false, 'msg'=>'ERROR: The interview instructions could not be resolved.');
		
		if(!empty($instructions['action']))
		{
			switch($instructions['action'])
			{
				case 'reject':
					$result['boolean'] = $this->cancel($instructions['id'],(!empty($instructions['reason'])? htmlentities($instructions['reason'], ENT_QUOTES): 'NONE'));
				break;
			}
			
			if(!empty($result['boolean'])) 
			{
				$result['msg'] = $result['boolean']? "The interview has been cancelled.": "ERROR: The interview could not be cancelled.";
			}
		}
		
		return $result;
	}
	
	
	
	
	# Cancel an interview
	function cancel($interviewId, $reason)
	{
		$item = $this->_query_reader->get_row_as_array('get_interview_vacancies', array('search_query'=>" I2.id='".$itemId."' ", 'limit_text'=>'1', 'order_by'=>' I2.date_added DESC '));
		
		# 1. Notify the applicant
		$result1 = $this->_messenger->send($item['applicant_id'], array('code'=>'notify_interview_cancellation', 'applicant_name'=>$item['applicant'], 'interviewer_name'=>$item['interviewer'], 'job_name'=>$item['job'], 'interview_date'=>date('d-M-Y h:ia T', strtotime($item['interview_date'])), 'action_date'=>date('d-M-Y h:ia T', strtotime('now')), 'reason'=>$reason));
		
		# 2. Delete the interview details from the database
		if($result1) $result2 = $this->_query_reader->run('remove_interview_details', array('interview_id'=>$interviewId));
		else $result2 = false;
		
		return get_decision(array($result1, $result2), FALSE);
	}
	
	
	
	
	# Populate the interview board details
	function populate_board($interviewId)
	{
		$boardList = $this->_query_reader->get_list('get_interview_board', array('interview_id'=>$interviewId));
		if(!empty($boardList))
		{
			# Since the board details are the same in all rows, use the first row for the data
			$this->native_session->set('boardname__boards', $boardList[0]['board_name']);
			$this->native_session->set('boardid', $boardList[0]['board_id']);
			
			$members = array();
			$length = count($boardList);
			$i = 1;
			$chairmanSet = false;
			foreach($boardList AS $row)
			{
				if($row['is_chairman'] == 'Y') $chairmanSet = true;
				# Forcefully set the last member as the chairman if none has been set
				if($length == $i && !$chairmanSet) $row['is_chairman'] = 'Y';
				
				array_push($members, array('member_id'=>$row['member_id'], 'member_name'=>$row['member_name'], 'is_chairman'=>$row['is_chairman']));
				$i++;
			}
			$this->native_session->set('boardmembers', $members);
		}
	}
	
	
	
	
	# Remove the interview board details from session
	function clear_board()
	{
		$this->native_session->delete_all(array('boardname__boards'=>'', 'boardid'=>'', 'boardmembers'=>''));
	}
	
	
	
	# Add interview board
	function add_board($interviewId, $details)
	{
		$result = false;
		
		if(!empty($details['boardname__boards']) && !empty($details['boardmembers']))
		{
			if($this->native_session->get('boardid') || !empty($details['boardid'])) $board = $this->_query_reader->get_row_as_array('get_board_by_id', array('board_id'=>(!empty($details['boardid'])? $details['boardid']: $this->native_session->get('boardid')) ));
			# Who is the chairman?
			$chairman = !empty($details['ischairman'])? $details['ischairman']: $details['boardmembers'][0];
				
			# Update the board if it is already available and the user used the same board name
			if(!empty($board) && (empty($details['boardid']) || (!empty($details['boardid']) && $details['boardname__boards'] == html_entity_decode($board['name'], ENT_QUOTES) )))
			{
				# 1. Remove previous board members
				$result1 = $this->_query_reader->run('remove_board_members', array('board_id'=>$board['id'] ));
				# 2. Add board members
				$result2 = $result1? $this->_query_reader->run('add_board_members', array('board_id'=>$board['id'], 'member_ids'=>"'".implode("','",$details['boardmembers'])."'", 'added_by'=>$this->native_session->get('__user_id'), 'chairman'=>$chairman )): false;
				# 3. Update interview with new board
				$result = $result2? $this->_query_reader->run('update_interview_board', array('interview_id'=>$interviewId, 'board_id'=>$board['id'], 'interviewer_id'=>$chairman, 'updated_by'=>$this->native_session->get('__user_id') )): false;
				
			}
			# Add a new board
			else
			{
				# 1. Add new board
				$boardId = $this->_query_reader->add_data('add_new_board', array('board_name'=>htmlentities($details['boardname__boards'], ENT_QUOTES), 'added_by'=>$this->native_session->get('__user_id') ));
				# 2. Add board members
				$result1 = $this->_query_reader->run('add_board_members', array('board_id'=>$boardId, 'member_ids'=>"'".implode("','",$details['boardmembers'])."'", 'added_by'=>$this->native_session->get('__user_id'), 'chairman'=>$chairman ));
				# 3. Update interview with new board
				$result = $result1? $this->_query_reader->run('update_interview_board', array('interview_id'=>$interviewId, 'board_id'=>$boardId, 'interviewer_id'=>$chairman, 'updated_by'=>$this->native_session->get('__user_id') )): false;
			}
		} 
		
		return $result;
	}
	
	
	
	
	# Add a member to the board
	function add_member_to_board($data)
	{
		$members = $this->native_session->get('boardmembers')? $this->native_session->get('boardmembers'): array();
		
		if(!empty($data['userid']) && !empty($data['membername__users']) && empty(get_row_from_list($members, 'member_id', $data['userid'])))
		{
			array_push($members, array('member_id'=>$data['userid'], 'member_name'=>restore_bad_chars($data['membername__users']), 'is_chairman'=>(empty($members)? 'Y': 'N') ));
			$this->native_session->set('boardmembers', $members);
			$msg = "Member has been added";
		}
		else if(!empty($data['userid']) && !empty(get_row_from_list($members, 'member_id', $data['userid'])))
		{
			$msg = "WARNING: Member is already on the board";
		}
		else $msg = "ERROR: We could not resolve the member data";
		
		return $msg;
	}
	
	
	
	
	# Remove a member from the board
	function remove_member_from_board($data)
	{
		$members = $this->native_session->get('boardmembers')? $this->native_session->get('boardmembers'): array();
		
		if(!empty($data['userid']) && !empty($members))
		{
			# Get the member row key
			$rowId = get_row_from_list($members, 'member_id', $data['userid'], 'key');
			
			if(!empty($rowId) || $rowId == 0) unset($members[$rowId]);
			$this->native_session->set('boardmembers', $members);
			$msg = "Member has been removed";
		}
		else $msg = "ERROR: We could not resolve the instruction";
		
		return $msg;
	}
	
	
	
	
	# Load the board member list
	function populate_board_list($data)
	{
		if(!empty($data['boardid']))
		{
			$boardList = $this->_query_reader->get_list('get_board_members', array('board_id'=>$data['boardid']));
			if(!empty($boardList))
			{
				$members = array();
				$length = count($boardList);
				$i = 1;
				$chairmanSet = false;
				foreach($boardList AS $row)
				{
					if($row['is_chairman'] == 'Y') $chairmanSet = true;
					# Forcefully set the last member as the chairman if none has been set
					if($length == $i && !$chairmanSet) $row['is_chairman'] = 'Y';
				
					array_push($members, array('member_id'=>$row['member_id'], 'member_name'=>$row['member_name'], 'is_chairman'=>$row['is_chairman']));
					$i++;
				}
				$this->native_session->set('boardmembers', $members);
			}
		}
		
		# Return appropriate message based on whether the board is empty or id can not be found
		return empty($boardList)? (!empty($data['boardid'])? "WARNING: This board has no members": "ERROR: Board members could not be loaded"): "";
	}
	
	
	
	
	# Set interviews for multiple users in one go
	function set_multiuser_interviews($details)
	{
		$result = false;
		
		if(!empty($details['applicationid']) && !empty($details['interviewdate']) && !empty($details['boardid']))
		{
			$board = $this->_query_reader->get_row_as_array('get_board_chairman',array('board_id'=>$details['boardid']));
			
			# 1. Record the interview dates
			$result1 = $this->_query_reader->run('record_multiple_interviews', array('application_ids'=>"'".implode("','",$details['applicationid'])."'", 'interviewer_id'=>$board['chairman_id'], 'planned_date'=>date('Y-m-d H:i:s', strtotime($details['interviewdate'])), 'notes'=>'NONE','board_id'=>$details['boardid'], 'added_by'=>$this->native_session->get('__user_id')));
			
			# 2. Notify the applicants and chairperson about the interviews
			if($result1 && !empty($board)) 
			{
				# a) Notify the applicants
				$applications =  $this->_query_reader->get_list('get_application_list_data', array('application_ids'=>"'".implode("','",$details['applicationid'])."'"));
				
				$results = $applicants = array();
				foreach($applications AS $row)
				{
					array_push($results, 
						$this->_messenger->send($row['applicant_id'], array('code'=>'notice_of_set_interview', 'applicant'=>$row['applicant_name'], 'submission_date'=>date('d-M-Y h:ia T', strtotime($row['submission_date'])), 'institution_name'=>$row['institution_name'], 'interview_notes'=>'NONE', 'planned_date'=>$details['interviewdate'], 'interviewer'=>$board['chairman_name']." (BOARD NAME: ".$board['board_name'].")" ))
					);
					
					array_push($applicants, $row['applicant_name']);
				}
				
				# Notify the chairman as well
				array_push($results, 
					$this->_messenger->send($board['chairman_id'], array('code'=>'notify_interviewer_of_date', 'applicant'=>implode(', ',$applicants), 'submission_date'=>'multiple submissions', 'institution_name'=>'multiple schools', 'interview_notes'=>'NONE', 'planned_date'=>$details['interviewdate'], 'interviewer'=>$board['chairman_name']." (BOARD NAME: ".$board['board_name'].")" ))
				);
					
					
				$result2 = get_decision($results, FALSE);
			}
			else $result2 = false;
			
			$result = get_decision(array($result1, $result2));
		}
		
		return $result;
	}
	
	
	
	
	
	
	
}


?>