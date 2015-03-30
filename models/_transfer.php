<?php
/**
 * This class manages transfer data.
 *
 * @author Al Zziwa <azziwa@gmail.com>
 * @version 1.1.0
 * @copyright TMIS
 * @created 02/06/2015
 */
class _transfer extends CI_Model
{
	
	
		
	
	# Get list of transfers
	function get_list($instructions=array())
	{
		$searchString = " 1=1 ";
		if(!empty($instructions['action']) && $instructions['action']== 'institutionapprove')
		{
			$searchString = " T.status='pending' ";
		}
		else if(!empty($instructions['action']) && $instructions['action']== 'countyapprove')
		{
			$searchString = " T.status='institutionapproved' ";
		}
		else if(!empty($instructions['action']) && $instructions['action']== 'pca')
		{
			$searchString = " T.status='countyapproved' ";
		}
		else if(!empty($instructions['action']) && $instructions['action']== 'ministryapprove')
		{
			$searchString = " T.status='pcaissued' ";
		}
		
		
		# Narrow the items viewed for the manager to their school(s) only
		if($this->native_session->get('__permission_group') == '3')
		{
			$searchString .= " AND T.teacher_id <> '".$this->native_session->get('__user_id')."' AND T.old_school_id IN ('".implode("','", $this->get_postings($this->native_session->get('__user_id')))."') ";
		}
		
		# If a search phrase is sent in the instructions
		if(!empty($instructions['searchstring']))
		{
			$searchString .= " AND ".$instructions['searchstring'];
		}
		
		# Instructions
		$count = !empty($instructions['pagecount'])? $instructions['pagecount']: NUM_OF_ROWS_PER_PAGE;
		$start = !empty($instructions['page'])? ($instructions['page']-1)*$count: 0;
		
		return $this->_query_reader->get_list('get_transfer_list_data',array('search_query'=>$searchString, 'limit_text'=>$start.','.($count+1), 'order_by'=>" T.last_updated DESC "));
	}

	
	
	
	
	
	
	
	
	# Verify a transfer
	function verify($instructions)
	{
		$this->load->model('_approval_chain');
		$result = array('boolean'=>false, 'msg'=>'ERROR: The transfer instructions could not be resolved.');
		
		if(!empty($instructions['action']))
		{
			# Get the transfer details if you are going to use them at this step
			if(current(explode("_", $instructions['action'])) == 'reject') 
			{
				$transfer = $this->details($instructions['id']);
				$recipients = array($transfer['teacher_id'], $transfer['last_updated_by']);
				
				$message = array('code'=>'notify_change_of_data_status', 'item'=>'Transfer Application', 'approver_name'=>$this->native_session->get('__full_name'), 'status'=>'REJECTED', 'action_date'=>date('d-M-Y h:ia T', strtotime('now')), 'details'=>"<br>Teacher Name: ".$transfer['teacher_name']."<br>Current School: ".$transfer['current_school_name']."<br>Desired School Name: ".$transfer['desired_school_name']." <br>Transfer Reason: ".$transfer['reason']."  ".(!empty($instructions['reason'])? "<br><br>Rejection Reason:<br>".htmlentities($instructions['reason'], ENT_QUOTES): '') );
			}
			
			
			switch($instructions['action'])
			{
				# Approve at institution level
				case 'approve_toinstitutionapprove':
					$result = $this->_approval_chain->add_chain($instructions['id'], 'transfer', '2', 'approved', (!empty($instructions['reason'])? htmlentities($instructions['reason'], ENT_QUOTES): 'NONE') );
					
					$result2 = $result['boolean']? $this->change_status($instructions['id'], 'institutionapproved'): false;
					$result['boolean'] = get_decision(array($result['boolean'], $result2));
					$result['msg'] = $result['boolean']?'The transfer has been approved': 'ERROR: We could not approve the transfer.';
				break;
				
				# Reject at institution level
				case 'reject_frominstitutionapprove':
					#Remove transfer application - if rejected at institution level
					$result1 = $this->_query_reader->run('remove_transfer_record', array('transfer_id'=>$instructions['id']));
					
					$result['boolean'] = $result1? $this->_messenger->send($recipients, $message): false;
					$result['msg'] = $result['boolean']?'The transfer has been rejected': 'ERROR: We could not reject the transfer.';
				break;
				
				# Approve at county level
				case 'approve_tocountyapprove':
					$instructions = process_other_field($instructions);
					$result = $this->_approval_chain->add_chain($instructions['id'], 'transfer', '3', 'approved', (!empty($instructions['reason'])? htmlentities($instructions['reason'], ENT_QUOTES): 'NONE'), array('minutenumber'=>restore_bad_chars($instructions['minutenumber'])) );
					
					$result2 = $result['boolean']? $this->change_status($instructions['id'], 'countyapproved'): false;
					$result['boolean'] = get_decision(array($result['boolean'], $result2));
					$result['msg'] = $result['boolean']?'The transfer has been approved': 'ERROR: We could not approve the transfer.';
				break;
				
				# Reject at county level
				case 'reject_fromcountyapprove':
					# Lower approval level if rejected
					$result1 = $this->change_status($instructions['id'], 'pending');
					
					$result['boolean'] = $result1? $this->_messenger->send($recipients, $message): false;
					$result['msg'] = $result['boolean']?'The transfer has been rejected': 'ERROR: We could not reject the transfer.';
				break;
				
				# Issue a PCA
				case 'approve_topca':
					$instructions = process_other_field($instructions);
					
					$result = $this->_approval_chain->add_chain($instructions['id'], 'transfer', '4', 'approved', (!empty($instructions['reason'])? htmlentities($instructions['reason'], ENT_QUOTES): 'NONE'), array('subjectlist'=>restore_bad_chars($instructions['subjectlist'])) );
					
					$result2 = $result['boolean']? $this->change_status($instructions['id'], 'pcaissued'): false;
					$result['boolean'] = get_decision(array($result['boolean'], $result2));
					$result['msg'] = $result['boolean']?'The PCA has been issued': 'ERROR: We could not issue the PCA.';
				break;
				
				# Reject PCA Issue
				case 'reject_frompca':
					# Lower approval level if rejected
					$result1 = $this->change_status($instructions['id'], 'institutionapproved');
					
					$result['boolean'] = $result1? $this->_messenger->send($recipients, $message): false;
					$result['msg'] = $result['boolean']?'The PCA issue has been rejected': 'ERROR: We could not reject the PCA issue.';
				break;
				
				# Approve transfer at ministry level
				case 'approve_toministryapprove':
					$result = $this->_approval_chain->add_chain($instructions['id'], 'transfer', '5', 'approved', (!empty($instructions['reason'])? htmlentities($instructions['reason'], ENT_QUOTES): 'NONE') );
					
					$result2 = $result['boolean']? $this->change_status($instructions['id'], 'confirmed'): false;
					$result['boolean'] = get_decision(array($result['boolean'], $result2));
					$result['msg'] = $result['boolean']?'The transfer has been confirmed': 'ERROR: We could not confirm the transfer.';
				break;
				
				# Reject transfer at ministry level
				case 'reject_fromministryapprove':
					# Lower approval level if rejected
					$result1 = $this->change_status($instructions['id'], 'countyapproved');
					
					$result['boolean'] = $result1? $this->_messenger->send($recipients, $message): false;
					$result['msg'] = $result['boolean']?'The transfer confirmation has been rejected': 'ERROR: We could not reject the transfer confirmation.';
				break;
			}
		}
		
		return $result;
	}
	
	
	
	
	# Changes the status of the transfer
	function change_status($transferId, $newStatus)
	{
		return $this->_query_reader->run('change_transfer_field', array('field_name'=>'status', 'field_value'=>$newStatus, 'updated_by'=>$this->native_session->get('__user_id'), 'transfer_id'=>$transferId ));
	}
	
	
	
	
	# Cancel a transfer
	function cancel()
	{
		return $this->_query_reader->run('remove_transfer_application', array('teacher_id'=>$this->native_session->get('__user_id') ));
	}
	
	
	
	# Submit a transfer application
	function submit_application($transferDetails)
	{
		$required = array('school__schools', 'schoolid', 'transferdate', 'transferreason');
		$passed = process_fields($this, $transferDetails, $required, array("(", ")", "-"));
		
		if($passed['boolean'])
		{
			$details = $passed['data'];
			
			$transferId = $this->_query_reader->add_data('add_transfer_application', array('teacher_id'=>$this->native_session->get('__user_id'), 'new_school_id'=>$details['schoolid'], 'reason'=>$details['transferreason'], 'proposed_date'=>date('Y-m-d', strtotime($details['transferdate'])), 'added_by'=>$this->native_session->get('__user_id') ));
			
			# Add the approval chain to notify the instution manager
			if(!empty($transferId)) 
			{
				$this->load->model('_approval_chain');
				$result = $this->_approval_chain->add_chain($transferId, 'transfer', '1', 'approved');
			}
			else $result = array('boolean'=>false, 'msg'=>'ERROR: We could not record your transfer application.');
		}
		else $result = array('boolean'=>false, 'msg'=>'WARNING: Please enter all required fields without invalid characters to continue.');
		
		return $result;
	}
	
	
	# Get the details of a transfer application
	function details($transferId)
	{
		return $this->_query_reader->get_row_as_array('get_transfer_list_data', array('search_query'=>" T.id='".$transferId."' ", 'order_by'=>' T.last_updated DESC ', 'limit_text'=>'1'));
	}
	
	
	# Get user transfer application
	function get_application($status='pending')
	{
		return $this->_query_reader->get_row_as_array('get_transfer_list_data', array('search_query'=>" T.teacher_id='".$this->native_session->get('__user_id')."' AND T.status='".$status."' ", 'order_by'=>' T.last_updated DESC ', 'limit_text'=>'1'));
	}
	
	
	#Get the postings of a user given their ID
	function get_postings($userId)
	{
		return $this->_query_reader->get_single_column_as_array('get_user_posting', 'institution_id', array('user_id'=>$userId));
	}
	
	
	
	#Set the current school details
	function get_current_school()
	{
		return $this->_query_reader->get_row_as_array('get_teacher_jobs', array('user_id'=>$this->native_session->get('__user_id'), 'search_condition'=>" AND P.posting_end_date = '0000-00-00' LIMIT 1" ));
	}
	
	
}


?>