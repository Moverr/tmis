<?php
/**
 * Manages leave data.
 *
 * @author Al Zziwa <azziwa@gmail.com>
 * @version 1.1.0
 * @copyright TMIS
 * @created 01/08/2015
 */
class _leave extends CI_Model
{
	
	# Submit a leave application
	function submit_application($leaveDetails)
	{
		$required = array('leavestartdate', 'leaveenddate', 'leavereason');
		$passed = process_fields($this, $leaveDetails, $required, array("(", ")", "-"));
		
		if($passed['boolean'] && (strtotime($leaveDetails['leavestartdate']) > strtotime('now')) && (strtotime($leaveDetails['leaveenddate']) > strtotime($leaveDetails['leavestartdate'])) )
		{
			$details = $passed['data'];
			
			$leaveId = $this->_query_reader->add_data('add_leave_application', array('teacher_id'=>$this->native_session->get('__user_id'), 'reason'=>$details['leavereason'], 'proposed_start_date'=>date('Y-m-d', strtotime($details['leavestartdate'])), 'proposed_end_date'=>date('Y-m-d', strtotime($details['leaveenddate'])), 'added_by'=>$this->native_session->get('__user_id') ));
			
			# Add the approval chain to notify the next approving party
			if(!empty($leaveId)) 
			{
				$this->load->model('_approval_chain');
				$result = $this->_approval_chain->add_chain($leaveId, 'leave', '1', 'approved');
			}
			else $result = array('boolean'=>false, 'msg'=>'ERROR: We could not record your leave application.');
		}
		else 
		{
			if(!(strtotime($leaveDetails['leavestartdate']) > strtotime('now')))
				$msg ='WARNING: The leave start date can not be earlier than the current time.';
			else if(!(strtotime($leaveDetails['leaveenddate']) > strtotime($leaveDetails['leavestartdate'])))
				$msg ='WARNING: The leave end date can not be earlier than the leave start date.';
			else 
				$msg = 'WARNING: Please enter all required fields without invalid characters to continue.';
			
			$result = array('boolean'=>false, 'msg'=>$msg);
		}
		
		return $result;
	}
		
		
	
	# Get list of leave
	function get_list($instructions=array())
	{
		$searchString = " 1=1 ";
		if(!empty($instructions['action']) && $instructions['action']== 'approve')
		{
			$searchString = " L.status='pending' ";
		}
		else if(!empty($instructions['action']) && $instructions['action']== 'verify')
		{
			$searchString = " L.status='districtapproved' ";
		}
		else if(!empty($instructions['action']) && $instructions['action']== 'send')
		{
			$searchString = " L.status IN ('confirmed','rejected') ";
		}
		
		
		# If a search phrase is sent in the instructions
		if(!empty($instructions['searchstring']))
		{
			$searchString .= " AND ".$instructions['searchstring'];
		}
		
		# Instructions
		$count = !empty($instructions['pagecount'])? $instructions['pagecount']: NUM_OF_ROWS_PER_PAGE;
		$start = !empty($instructions['page'])? ($instructions['page']-1)*$count: 0;
		
		return $this->_query_reader->get_list('get_leave_list_data',array('search_query'=>$searchString, 'limit_text'=>$start.','.($count+1), 'order_by'=>" L.last_updated DESC "));
	}

	
	
	
	
	
	
	
	# Verify a transfer
	function verify($instructions)
	{
		$this->load->model('_approval_chain');
		$result = array('boolean'=>false, 'msg'=>'ERROR: The leave instructions could not be resolved.');
		
		if(!empty($instructions['action']))
		{
			# Get the transfer details if you are going to use them at this step
			if(current(explode("_", $instructions['action'])) == 'reject') 
			{
				$leave = $this->details($instructions['id']);
				$recipients = array($leave['teacher_id'], $leave['last_updated_by']);
				
				$message = array('code'=>'notify_change_of_data_status', 'item'=>'Leave Application', 'approver_name'=>$this->native_session->get('__full_name'), 'status'=>'REJECTED', 'action_date'=>date('d-M-Y h:ia T', strtotime('now')), 'details'=>"<br>Teacher Name: ".$leave['teacher_name']."<br>School: ".$leave['school_name']." <br>Leave Reason: ".$leave['reason']."  ".(!empty($instructions['reason'])? "<br><br>Rejection Reason:<br>".htmlentities($instructions['reason'], ENT_QUOTES): '') );
			}
			
			
			switch($instructions['action'])
			{
				# Approve at county level
				case 'approve_toapprove':
					$result = $this->_approval_chain->add_chain($instructions['id'], 'leave', '2', 'approved', (!empty($instructions['reason'])? htmlentities($instructions['reason'], ENT_QUOTES): 'NONE') );
					
					$result2 = $result['boolean']? $this->change_status($instructions['id'], 'districtapproved'): false;
					$result['boolean'] = get_decision(array($result['boolean'], $result2));
					$result['msg'] = $result['boolean']?'The leave has been approved': 'ERROR: We could not approve the leave.';
				break;
				
				# Reject at county level
				case 'reject_fromapprove':
					#Remove leave application - if rejected at county level
					$result1 = $this->_query_reader->run('remove_leave_record', array('leave_id'=>$instructions['id']));
					
					$result['boolean'] = $result1? $this->_messenger->send($recipients, $message): false;
					$result['msg'] = $result['boolean']?'The leave has been rejected': 'ERROR: We could not reject the leave.';
				break;
				
				# Approve at ministry level
				case 'approve_toverify':
					$result = $this->_approval_chain->add_chain($instructions['id'], 'leave', '3', 'approved', (!empty($instructions['reason'])? htmlentities($instructions['reason'], ENT_QUOTES): 'NONE'));
					
					$result2 = $result['boolean']? $this->change_status($instructions['id'], 'confirmed'): false;
					$result['boolean'] = get_decision(array($result['boolean'], $result2));
					$result['msg'] = $result['boolean']?'The leave has been approved': 'ERROR: We could not approve the leave.';
				break;
				
				# Reject at ministry level
				case 'reject_fromverify':
					# Change status to rejected
					$result1 = $this->change_status($instructions['id'], 'rejected');
					
					$result['boolean'] = $result1? $this->_messenger->send($recipients, $message): false;
					$result['msg'] = $result['boolean']?'The transfer has been rejected': 'ERROR: We could not reject the transfer.';
				break;
				
				# Send a leave approval letter
				case 'approve_tosend':
					$instructions = process_other_field($instructions);
					
					$result = $this->_approval_chain->add_chain($instructions['id'], 'leave', '4', 'approved', (!empty($instructions['reason'])? htmlentities($instructions['reason'], ENT_QUOTES): 'NONE'), 
						array('minutenumber'=>restore_bad_chars($instructions['minutenumber']), 
							'leavetype'=>$instructions['leavetype__leavetypes'], 
							'startdate'=>$instructions['startdate'],
							'enddate'=>$instructions['enddate'],
							'leavereason'=>htmlentities($instructions['reason'], ENT_QUOTES)
							) );
					
					$result2 = $result['boolean']? $this->_query_reader->run('update_leave_dates', array('leave_id'=>$instructions['id'], 'actual_start_date'=>date('Y-m-d', strtotime($instructions['startdate'])), 'actual_end_date'=>date('Y-m-d', strtotime($instructions['enddate'])) )): false;
					$result['boolean'] = get_decision(array($result['boolean'], $result2));
					$result['msg'] = $result['boolean']?'The leave approval letter has been sent': 'ERROR: We could not send the leave approval letter.';
				break;
				
				# Send a leave rejection notice
				case 'reject_fromsend':
					$result['boolean'] = $result1? $this->_messenger->send($recipients, $message): false;
					$result['msg'] = $result['boolean']?'The leave has been rejected': 'ERROR: We could not reject the leave.';
				break;
			}
		}
		
		return $result;
	}
	
	
	
	
	# Changes the status of the leave
	function change_status($leaveId, $newStatus)
	{
		return $this->_query_reader->run('change_leave_field', array('field_name'=>'status', 'field_value'=>$newStatus, 'updated_by'=>$this->native_session->get('__user_id'), 'leave_id'=>$leaveId ));
	}
	
	
	
	
	# Get the details of a leave application
	function details($leaveId)
	{
		return $this->_query_reader->get_row_as_array('get_leave_list_data', array('search_query'=>" L.id='".$leaveId."' ", 'order_by'=>' L.last_updated DESC ', 'limit_text'=>'1'));
	}
	
	
	# Get user leave application
	function get_application($status='pending')
	{
		return $this->_query_reader->get_row_as_array('get_leave_list_data', array('search_query'=>" L.teacher_id='".$this->native_session->get('__user_id')."' AND L.status='".$status."' ", 'order_by'=>' L.last_updated DESC ', 'limit_text'=>'1'));
	}
	
	
	
	
	# Cancel a leave
	function cancel()
	{
		return $this->_query_reader->run('remove_leave_application', array('teacher_id'=>$this->native_session->get('__user_id') ));
	}
	
	
	
	#Set the current school details
	function get_current_school()
	{
		return $this->_query_reader->get_row_as_array('get_teacher_jobs', array('user_id'=>$this->native_session->get('__user_id'), 'search_condition'=>" AND P.posting_end_date = '0000-00-00' LIMIT 1" ));
	}
}


?>