<?php
/**
 * This class manages vacancy data in the system.
 *
 * @author Al Zziwa <azziwa@gmail.com>
 * @version 1.1.0
 * @copyright TMIS
 * @created 01/08/2015
 */
class _vacancy extends CI_Model
{
	#Constructor to set some default values at class load
	public function __construct()
    {
        parent::__construct();
		$this->load->model('_approval_chain');
	}
	
	# Add a new vacancy.
	function add_new($vacancyDetails)
	{
		$isAdded = false;
		$required = array('institution__institutions', 'role__jobroles', 'headline', 'summary', 'details', 'publishstart', 'publishend');
		
		# 1. Add all provided data into the session
		$passed = process_fields($this, $vacancyDetails, $required, array("/", "<", ">", "\"", "=", "(", ")", "!", "#", "%", "&", "?", ":", ";", "'"));
		$msg = !empty($passed['msg'])? $passed['msg']: "";
		# 2. Save the data into the database
		if($passed['boolean'])
		{
			$details = $passed['data'];
			$vacancyId = $this->_query_reader->add_data('add_vacancy_data', array('institution'=>$details['institution__institutions'], 'role'=>$details['role__jobroles'], 'topic'=>$details['headline'], 'summary'=>$details['summary'], 'details'=>$details['details'], 'start_date'=>format_date($details['publishstart'], 'YYYY-MM-DD'), 'end_date'=>format_date($details['publishend'], 'YYYY-MM-DD'), 'added_by'=>$this->native_session->get('__user_id') ));
			
			 $isAdded = !empty($vacancyId)? true: false;
			 if($isAdded) 
			 {
				 # Notify approving parties
				 $result = $this->_approval_chain->add_chain($vacancyId, 'vacancy', '1', 'approved');
				 $msg = $result['boolean']? "The data has been saved and the approving parties have been notified.": $result['msg'];
				 $this->native_session->delete_all($details);
			 }
		}
		
		return array('boolean'=>$isAdded, 'msg'=>$msg, 'id'=>(!empty($vacancyId)? $vacancyId: ''));
	}
		
	
		
	
	# Update a vacancy
	function update($vacancyId, $vacancyDetails)
	{
		$isUpdated = false;
		$required = array('headline', 'summary', 'details', 'publishstart', 'publishend');
		# 1. Add all provided data into the session
		$passed = process_fields($this, $vacancyDetails, $required, array("/", "<", ">", "\"", "=", "(", ")", "!", "#", "%", "&", "?", ":", ";"));
		$msg = !empty($passed['msg'])? $passed['msg']: "";
		# 2. Save the data into the database
		if($passed['boolean'])
		{
			$details = $passed['data'];
			$isUpdated = $this->_query_reader->run('update_vacancy_data', array('topic'=>$details['headline'], 'summary'=>$details['summary'], 'details'=>$details['details'], 'start_date'=>format_date($details['publishstart'], 'YYYY-MM-DD'), 'end_date'=>format_date($details['publishend'], 'YYYY-MM-DD'), 'vacancy_id'=>$vacancyId, 'updated_by'=>$this->native_session->get('__user_id') ));
			
			if($isUpdated) $this->native_session->delete_all($details);
		}
		
		return array('boolean'=>$isUpdated, 'msg'=>$msg, 'id'=>$vacancyId);
	}	
		
	
	# Change the status of the vacancy
	function change_status($vacancyId, $newStatus)
	{
		return $this->_query_reader->run('update_vacancy_status', array('vacancy_id'=>$vacancyId, 'status'=>$newStatus, 'updated_by'=>$this->native_session->get('__user_id')));
	}
	
		
		
	
	# Get list of vacancies
	function get_list($instructions=array())
	{
		$searchString = " V.status='published' ";
		$orderBy = " V.date_added DESC ";
		if(!empty($instructions['action']) && $instructions['action']== 'publish')
		{
			$searchString = " V.status IN ('saved','verified') ";
		}
		else if(!empty($instructions['action']) && $instructions['action']== 'archive')
		{
			$searchString = " V.status IN ('saved','archived') ";
		}
		else if(!empty($instructions['action']) && $instructions['action']== 'verify')
		{
			$searchString = " V.status='saved' ";
		}
		else if(!empty($instructions['action']) && $instructions['action']== 'public')
		{
			$searchString = " V.status='published' AND (DATE(V.start_date) < NOW() AND DATE(V.end_date) > NOW()) ";
			$orderBy = " V.end_date ASC ";
		}
		
		# If a search phrase is sent in the instructions
		if(!empty($instructions['searchstring']))
		{
			$searchString .= " AND ".$instructions['searchstring'];
		}
		
		# Instructions
		$count = !empty($instructions['pagecount'])? $instructions['pagecount']: NUM_OF_ROWS_PER_PAGE;
		$start = !empty($instructions['page'])? ($instructions['page']-1)*$count: 0;
		
		return $this->_query_reader->get_list('get_vacancy_list_data',array('search_query'=>$searchString, 'limit_text'=>$start.','.($count+1), 'order_by'=>$orderBy));
	}
	
	
	# Get details of a vacancy
	function get_details($vacancyId)
	{
		return $this->_query_reader->get_row_as_array('get_vacancy_by_id', array('vacancy_id'=>$vacancyId));
	}


	# Populate a vacancy session profile
	function populate_session($vacancyId)
	{
		$details = $this->_query_reader->get_row_as_array('get_vacancy_by_id', array('vacancy_id'=>$vacancyId));
		if(!empty($details))
		{
			$this->native_session->set('institution__institutions', $details['institution_name']);
			$this->native_session->set('role__jobroles', $details['role_name']);
			$this->native_session->set('headline', $details['topic']);
			$this->native_session->set('summary', $details['summary']);
			$this->native_session->set('details', $details['details']);
			$this->native_session->set('publishstart', date('d-M-Y', strtotime($details['start_date'])));
			$this->native_session->set('publishend', date('d-M-Y', strtotime($details['end_date'])));
		}
	}
	


	# Clear a vacancy session profile
	function clear_session()
	{
		$fields = array('institution__institutions'=>'', 'role__jobroles'=>'', 'headline'=>'', 'summary'=>'', 'details'=>'', 'publishstart'=>'', 'publishend'=>'');
		$this->native_session->delete_all($fields);
	}
	
	
	
	
	# Approve or reject a vacancy
	function verify($instructions)
	{
		$result = array('boolean'=>false, 'msg'=>'ERROR: The vacancy verification instructions could not be resolved.');
		
		if(!empty($instructions['action']))
		{
			switch($instructions['action'])
			{
				case 'approve_toverify':
					$result = $this->_approval_chain->add_chain($instructions['id'], 'vacancy', '2', 'approved', (!empty($instructions['reason'])? htmlentities($instructions['reason'], ENT_QUOTES): 'NONE') );
					$this->change_status($instructions['id'], 'verified'); 
				break;
				
				case 'reject_fromverify':
					$result = $this->_approval_chain->add_chain($instructions['id'], 'vacancy', '2', 'rejected', (!empty($instructions['reason'])? htmlentities($instructions['reason'], ENT_QUOTES): 'NONE') );
				break;
				
				case 'approve_topublish':
					$result = $this->_approval_chain->add_chain($instructions['id'], 'vacancy', '3', 'approved', (!empty($instructions['reason'])? htmlentities($instructions['reason'], ENT_QUOTES): 'NONE') );
					# The vacancy status is changed by the approval chain
				break;
				
				case 'reject_frompublish':
					$result = $this->_approval_chain->add_chain($instructions['id'], 'vacancy', '3', 'rejected', (!empty($instructions['reason'])? htmlentities($instructions['reason'], ENT_QUOTES): 'NONE') );
				break;
				
				case 'archive':
					$result = $this->change_status($instructions['id'], 'archived');
				break;
				
				case 'restore':
					$result = $this->change_status($instructions['id'], 'saved');
				break;
			}
		}
		
		return $result;
	}
	
	
	
	
}


?>