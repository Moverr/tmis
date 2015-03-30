<?php
/**
 * This class manages retirement data.
 *
 * @author Al Zziwa <azziwa@gmail.com>
 * @version 1.1.0
 * @copyright TMIS
 * @created 02/08/2015
 */
class _retirement extends CI_Model
{
	
	
	# Get list of retirements
	function get_list($instructions=array())
	{
		$searchString = " 1=1 ";
		if(!empty($instructions['action']) && $instructions['action']== 'approve')
		{
			$searchString = " R.status='pending' ";
		}
		else if(!empty($instructions['action']) && $instructions['action']== 'report')
		{
			$searchString = " R.status='confirmed' ";
		}
		
		# Narrow the items viewed for the manager to their school(s) only
		if($this->native_session->get('__permission_group') == '3')
		{
			$searchString .= " AND PS.institution_id IN ('".implode("','", $this->get_postings($this->native_session->get('__user_id')))."') AND retiree_id <> '".$this->native_session->get('__user_id')."' ";
		}
		
		# If a search phrase is sent in the instructions
		if(!empty($instructions['searchstring']))
		{
			$searchString .= " AND ".$instructions['searchstring'];
		}
		
		# Instructions
		$count = !empty($instructions['pagecount'])? $instructions['pagecount']: NUM_OF_ROWS_PER_PAGE;
		$start = !empty($instructions['page'])? ($instructions['page']-1)*$count: 0;
		
		return $this->_query_reader->get_list('get_retirement_list_data',array('search_query'=>$searchString, 'limit_text'=>$start.','.($count+1), 'order_by'=>" R.last_updated DESC "));
	}

	
	
	
	
	
	
	
	
	# Verify an retirement
	function verify($instructions)
	{
		$this->load->model('_approval_chain');
		$result = array('boolean'=>false, 'msg'=>'ERROR: The retirement instructions could not be resolved.');
		
		if(!empty($instructions['action']))
		{
			# Get the retirement details if you are going to use them at this step
			$retirement = $this->details($instructions['id']);
			
			if(current(explode("_", $instructions['action'])) == 'reject') 
			{
				$recipients = array($retirement['retiree_id'], $retirement['last_updated_by']);
				
				$message = array('code'=>'notify_change_of_data_status', 'item'=>'Retirement Application', 'approver_name'=>$this->native_session->get('__full_name'), 'status'=>'REJECTED', 'action_date'=>date('d-M-Y h:ia T', strtotime('now')), 'details'=>"<br>Teacher Name: ".$retirement['retiree_name']."<br>Reason: ".$retirement['retiree_reason']."<br>Proposed Date: ".format_date($retirement['proposed_date'],'d-M-Y').(!empty($instructions['reason'])? "<br><br>Rejection Reason:<br>".htmlentities($instructions['reason'], ENT_QUOTES): '') );
			}
			
			
			switch($instructions['action'])
			{
				# Approve retirement
				case 'approve':
					$result = $this->_approval_chain->add_chain($retirement['retiree_id'], 'retirement', '2', 'approved', (!empty($instructions['reason'])? htmlentities($instructions['reason'], ENT_QUOTES): 'NONE'), array('reason'=>(!empty($instructions['reason'])? htmlentities($instructions['reason'], ENT_QUOTES): 'NONE')) );
					
					$result2 = $this->change_status($instructions['id'], 'confirmed');
					$result['boolean'] = get_decision(array($result['boolean'], $result2));
					$result['msg'] = $result['boolean']?'The retirement has been approved': 'ERROR: We could not approve the retirement.';
				break;
				
				# Reject retirement
				case 'reject':
					#Remove retirement application - if rejected
					$result1 = $this->_query_reader->run('remove_retirement_record', array('retirement_id'=>$instructions['id']));
					
					$result['boolean'] = $result1? $this->_messenger->send($recipients, $message): false;
					$result['msg'] = $result['boolean']?'The retirement has been rejected': 'ERROR: We could not reject the retirement.';
				break;
				
			}
		}
		
		return $result;
	}
	
	
	
	
	# Changes the status of the retirement
	function change_status($retirementId, $newStatus)
	{
		return $this->_query_reader->run('change_retirement_field', array('field_name'=>'status', 'field_value'=>$newStatus, 'updated_by'=>$this->native_session->get('__user_id'), 'retirement_id'=>$retirementId ));
	}
	
	
	
	# Cancel an retirement
	function cancel()
	{
		return $this->_query_reader->run('remove_retirement_application', array('retiree_id'=>$this->native_session->get('__user_id') ));
	}
	
	
	
	# Submit a retirement application
	function submit_application($details)
	{
		$result1 = $this->_query_reader->run('add_retirement_application', array('retiree_id'=>$this->native_session->get('__user_id'), 'retiree_reason'=>(!empty($details['retirementreason'])? htmlentities($details['retirementreason'], ENT_QUOTES): 'NONE'), 'proposed_date'=>date('Y-m-d', strtotime($details['retirementdate'])), 'added_by'=>$this->native_session->get('__user_id') ));
		
		# Add the approval chain to notify the instution manager
		if($result1) 
		{
			$this->load->model('_approval_chain');
			$result = $this->_approval_chain->add_chain($this->native_session->get('__user_id'), 'retirement', '1', 'approved');
		}
		else $result = array('boolean'=>false, 'msg'=>'ERROR: We could not record your retirement application.');
		
		return $result;
	}
	
	
	# Get the details of a retirement application
	function details($applicationId)
	{
		return $this->_query_reader->get_row_as_array('get_retirement_application', array('application_id'=>$applicationId));
	}
	
	
	#Get the postings of a user given their ID
	function get_postings($userId)
	{
		return $this->_query_reader->get_single_column_as_array('get_user_posting', 'institution_id', array('user_id'=>$userId));
	}
	
	
	# Get user retirement application
	function get_application($status='pending')
	{
		return $this->_query_reader->get_row_as_array('get_retirement_list_data', array('search_query'=>" R.retiree_id='".$this->native_session->get('__user_id')."' AND R.status='".$status."' ", 'order_by'=>' R.last_updated DESC ', 'limit_text'=>'1'));
	}
	
	
}


?>