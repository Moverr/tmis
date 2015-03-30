<?php
/**
 * This class manages confirmation data.
 *
 * @author Al Zziwa <azziwa@gmail.com>
 * @version 1.1.0
 * @copyright TMIS
 * @created 02/06/2015
 */
class _confirmation extends CI_Model
{
	#Constructor to set some default values at class load
	public function __construct()
    {
        parent::__construct();
		$this->load->model('_approval_chain');
	}
	
	
	# Get list of confirmations
	function get_list($instructions=array())
	{
		$searchString = " 1=1 ";
		
		#Filter out un-needed results by status
		if(!empty($instructions['action']) && $instructions['action'] == 'post')
		{
			$searchString .= " AND P.status = 'saved' ";
		}
		else if(!empty($instructions['action']) && $instructions['action'] == 'view')
		{
			$searchString .= " AND P.applied_for_confirmation = 'Y' ";
		}
		else if(!empty($instructions['action']) && $instructions['action'] == 'approve')
		{
			$searchString .= " AND P.status = 'pending' AND P.applied_for_confirmation = 'Y' ";
		}
		else if(!empty($instructions['action']) && $instructions['action'] == 'verify')
		{
			$searchString .= " AND P.status IN ('confirmed','verified') ";
		}
		
		
		# Narrow the items viewed for the manager to their school(s) only
		if($this->native_session->get('__permission_group') == '3')
		{
			$searchString .= " AND I.id = '".$this->get_current_school('id')."' ";
		}
		#Narrow the items viewed for the cao to their district only
		else if($this->native_session->get('__permission_group') == '12')
		{
			$searchString .= " AND ((A.county <>'' AND A.county = '".$this->_messenger->get_address_part($this->native_session->get('__user_id'), 'county')."') OR (A.district_id <>'' AND A.district_id = '".$this->_messenger->get_address_part($this->native_session->get('__user_id'), 'district_id')."')) ";
		}
		
		
		# If a search phrase is sent in the instructions
		if(!empty($instructions['searchstring']))
		{
			$searchString .= " AND ".$instructions['searchstring'];
		}
		
		# Instructions
		$count = !empty($instructions['pagecount'])? $instructions['pagecount']: NUM_OF_ROWS_PER_PAGE;
		$start = !empty($instructions['page'])? ($instructions['page']-1)*$count: 0;
		
		return $this->_query_reader->get_list('get_job_postings',array('search_query'=>$searchString, 'limit_text'=>$start.','.($count+1), 'order_by'=>' P.last_updated DESC '));
	}

	
	
	#Get the postings of a user given their ID
	function get_postings($userId)
	{
		return $this->_query_reader->get_single_column_as_array('get_user_posting', 'institution_id', array('user_id'=>$userId));
	}
	
	
	
	
	#Set the current school details
	function get_current_school($returnType='array')
	{
		$school = $this->_query_reader->get_row_as_array('get_teacher_jobs', array('user_id'=>$this->native_session->get('__user_id'), 'search_condition'=>" AND P.posting_end_date = '0000-00-00' LIMIT 1" ));
		
		return ($returnType == 'id')? (!empty($school['institution_id'])? $school['institution_id']: ''): $school;
	}
	
	
	
	# Verify a confirmations
	function verify($instructions)
	{ 
		$result = array('boolean'=>false, 'msg'=>'ERROR: The confirmation verification instructions could not be resolved.');
		
		if(!empty($instructions['action']))
		{
			switch($instructions['action'])
			{
				# Post the user to job awarded
				case 'approve':
					$result = $this->change_status($instructions['id'], 'pending', 'posting');
				break;
				
				# Reject job award
				case 'reject':
					$result = $this->change_status($instructions['id'], 'reject', 'posting', (!empty($instructions['reason'])? htmlentities($instructions['reason'], ENT_QUOTES): 'NONE'));
				break;
				
				# issue job confirmation
				case 'approve_toapprove':
					$instructions = process_other_field($instructions);
					$result = $this->_approval_chain->add_chain($instructions['id'], 'confirmation', '2', 'approved', (!empty($instructions['reason'])? htmlentities($instructions['reason'], ENT_QUOTES): 'NONE'), array('minutenumber'=>restore_bad_chars($instructions['minutenumber'])) );
					
					$this->change_status($instructions['id'], 'confirmed', 'confirmation'); 
				break;
				
				# reject job confirmation
				case 'reject_fromapprove':
					$result = $this->_approval_chain->add_chain($instructions['id'], 'confirmation', '2', 'rejected', (!empty($instructions['reason'])? htmlentities($instructions['reason'], ENT_QUOTES): 'NONE') );
					$result2 = $this->_query_reader->run('change_posting_field', array('field_name'=>'applied_for_confirmation', 'field_value'=>'N', 'updated_by'=>$this->native_session->get('__user_id'), 'posting_id'=>$postingId )); 
					
					$result['boolean'] = get_decision(array($result['boolean'], $result2));
					$result['msg'] = (!$result['boolean']? 'ERROR: The user could not be rejected.': $result['msg']);
				break;
				
				# verify job confirmation
				case 'approve_toverify':
					$result = $this->_approval_chain->add_chain($instructions['id'], 'confirmation', '3', 'approved', (!empty($instructions['reason'])? htmlentities($instructions['reason'], ENT_QUOTES): 'NONE') );
					$this->change_status($instructions['id'], 'verified', 'confirmation'); 
				break;
				
				# reject job confirmation verification
				case 'reject_fromverify':
					$result = $this->_approval_chain->add_chain($instructions['id'], 'confirmation', '3', 'rejected', (!empty($instructions['reason'])? htmlentities($instructions['reason'], ENT_QUOTES): 'NONE') );
				break;
				
				# archive active job confirmation
				case 'archive':
					$result = $this->change_status($instructions['id'], 'reversed', 'confirmation');
				break;
			}
		}
		
		return $result;
	}
	
	
	
	# Changes the status of the posting/confirmation
	function change_status($postingId, $newStatus, $itemName, $reason = '')
	{
		# Use more understandable status words
		$status = array('pending'=>'approved', 'reject'=>'rejected', 'confirmed'=>'approved', 'verified'=>'verified', 'reversed'=>'reversed');
		
		if($newStatus != 'reject') $result1 = $this->_query_reader->run('change_posting_field', array('field_name'=>'status', 'field_value'=>$newStatus, 'updated_by'=>$this->native_session->get('__user_id'), 'posting_id'=>$postingId ));
		
		# notify the teacher who had been posted about the change in status
		# Other status change messaging is covered in the approval chain so do not send the notification twice
		if(in_array($newStatus, array('reversed', 'pending', 'reject')))
		{
			$posting = $this->_query_reader->get_row_as_array('get_job_postings',array('search_query'=>" P.id='".$postingId."' ", 'limit_text'=>'1', 'order_by'=>' P.last_updated DESC ')); 
			
			$result2 = $this->_messenger->send(array($posting['postee_id'], $posting['last_updated_by']), array('code'=>'notify_change_of_data_status', 'item'=>$itemName, 'approver_name'=>$this->native_session->get('__full_name'), 'status'=>strtoupper($status[$newStatus]), 'action_date'=>date('d-M-Y h:ia T', strtotime('now')), 'details'=>"<br>Job Name: ".$posting['job']."<br>School: ".$posting['institution_name']."<br>Awarded On: ".$posting['award_date']." ".(!empty($reason)? "<br>Reason:<br>".$reason: '') ));
		} 
		else $result2 = true;
		
		#Delete the rejected posting after sending
		if($newStatus == 'reject') 
		{
			#Roll-back the interview result
			$result1_1 = $this->_query_reader->run('update_interview_part', array('field_name'=>'result', 'field_value'=>'passed', 'interview_id'=>$posting['final_interview_id'] ));
			$result1_2 = $this->_query_reader->run('remove_posting_data', array('posting_id'=>$postingId ));
			
			$result1 = get_decision(array($result1_1, $result1_2));
		}
		
		$result = get_decision(array($result1, $result2));
		return array('boolean'=>$result, 'msg'=>($result? 'The '.$itemName.' has been '.$status[$newStatus].'.' : 'ERROR: The '.$itemName.' could not be '.$status[$newStatus].'.'));
	}
}


?>