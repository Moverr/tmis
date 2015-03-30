<?php
/**
 * This class creates and manages school data.
 *
 * @author Al Zziwa <azziwa@gmail.com>
 * @version 1.1.0
 * @copyright TMIS
 * @created 01/30/2015
 */
class _school extends CI_Model
{
	#Constructor to set some default values at class load
	public function __construct()
    {
        parent::__construct();
		$this->load->model('_approval_chain');
	}
	
	# Add a new school
	function add_new($schoolDetails)
	{
		$isAdded = false;
		$required = array('schoolname', 'schooltype__schooltypes', 'dateschoolregistered', 'schooltelephone', 'schooladdress__addressline');
		
		# 1. Add all provided data into the session
		$passed = process_fields($this, $schoolDetails, $required, array("-"));
		$msg = !empty($passed['msg'])? $passed['msg']: "";
		# 2. Save the data into the database
		if($passed['boolean'])
		{
			$details = $passed['data'];
			$schoolId = $this->_query_reader->add_data('add_school_data', array('name'=>$details['schoolname'], 'school_type'=>$details['schooltype__schooltypes'], 'start_date'=>format_date($details['dateschoolregistered'], 'YYYY-MM-DD'), 'added_by'=>$this->native_session->get('__user_id') ));
			
			# Add the contact details if the root school record has been added
			if(!empty($schoolId)) 
			{
				# Address
				if($this->native_session->get('schooladdress__addressline'))
				{
					$locationId = $this->add_address($schoolId, array('address_type'=>'physical', 'importance'=>'contact', 'details'=>htmlentities($this->native_session->get('schooladdress__addressline'), ENT_QUOTES), 'district'=>$this->native_session->get('schooladdress__district'), 'country'=>$this->native_session->get('schooladdress__country'), 'county'=>($this->native_session->get('schooladdress__county')? $this->native_session->get('schooladdress__county'): "") ));
				}
				
				# Telephone
				$phoneContactId = $this->_query_reader->add_data('add_contact_data', array('contact_type'=>'telephone', 'carrier_id'=>'', 'details'=>$details['schooltelephone'], 'parent_id'=>$schoolId, 'parent_type'=>'school'));
				
				# Email
				if(!empty($details['schoolemailaddress']))
				{
					$emailContactId = $this->_query_reader->add_data('add_contact_data', array('contact_type'=>'email', 'carrier_id'=>'', 'details'=>$details['schoolemailaddress'], 'parent_id'=>$schoolId, 'parent_type'=>'school'));
				}
			}
			
			 $isAdded = !empty($schoolId)? true: false;
			 if($isAdded) 
			 {
				 # Notify approving parties
				 $result = $this->_approval_chain->add_chain('school|'.$schoolId, 'data', '1', 'approved');
				 $msg = $result['boolean']? "The data has been saved and the approving parties have been notified.": $result['msg'];
				 $this->native_session->delete_all($details);
			 }
		}
		
		return array('boolean'=>$isAdded, 'msg'=>$msg, 'id'=>(!empty($schoolId)? $schoolId: ''));
	}
	
	
	
	
	# Add a school's address
	function add_address($schoolId, $addressDetails)
	{
		return $this->_query_reader->add_data('add_new_address', array('parent_id'=>$schoolId, 'parent_type'=>'school', 'address_type'=>$addressDetails['address_type'], 'importance'=>$addressDetails['importance'], 'details'=>$addressDetails['details'], 'county'=>$addressDetails['county'], 'district'=>$addressDetails['district'], 'country'=>$addressDetails['country']));
	}
	
	
		
	
		
	
	# Update a school
	function update($schoolId, $schoolDetails)
	{
		$isUpdated = false;
		$required = array('schooltelephone');
		# 1. Add all provided data into the session
		$passed = process_fields($this, $schoolDetails, $required, array("-"));
		$msg = !empty($passed['msg'])? $passed['msg']: "";
		# 2. Save the data into the database
		if($passed['boolean'])
		{
			$details = $passed['data'];
			$school = $this->_query_reader->get_row_as_array('get_school_by_id', array('school_id'=>$schoolId));
			
			# Telephone
			$isUpdated = $this->_query_reader->run((!empty($school['telephone'])? 'update_contact_data': 'add_contact_data'), array('contact_type'=>'telephone', 'carrier_id'=>'', 'details'=>$details['schooltelephone'], 'parent_id'=>$schoolId, 'parent_type'=>'school'));
				
			# Email
			if($isUpdated && !empty($details['schoolemailaddress']))
			{
				$isUpdated = $this->_query_reader->run((!empty($school['email_address'])? 'update_contact_data': 'add_contact_data'), array('contact_type'=>'email', 'carrier_id'=>'', 'details'=>$details['schoolemailaddress'], 'parent_id'=>$schoolId, 'parent_type'=>'school'));
				
				$msg = $isUpdated? "The updates have been applied.": "ERROR: We could not update the email address.";
			}
			
			if($isUpdated) $this->native_session->delete_all($details);
		}
		
		return array('boolean'=>$isUpdated, 'msg'=>$msg, 'id'=>$schoolId);
	}
	
	
	

	# Clear a school session profile
	function clear_session()
	{
		$fields = array('schoolname'=>'', 'schooltype__schooltypes'=>'', 'dateschoolregistered'=>'', 'schoolemailaddress'=>'', 'schooltelephone'=>'', 'schooladdress__addressline'=>'', 'schooladdress__district'=>'', 'schooladdress__country'=>'', 'schooladdress__county'=>'');
		$this->native_session->delete_all($fields);
	}
		
	

	# Populate a school session profile
	function populate_session($schoolId)
	{
		$school = $this->_query_reader->get_row_as_array('get_school_by_id', array('school_id'=>$schoolId));
		if(!empty($school))
		{
			$this->native_session->set('schoolname', $school['name']);
			$this->native_session->set('schooltype__schooltypes', $school['school_type']);
			$this->native_session->set('dateschoolregistered', format_date($school['date_registered'],'d-M-Y',''));
			if(!empty($school['email_address'])) $this->native_session->set('schoolemailaddress', $school['email_address']);
			$this->native_session->set('schooltelephone', $school['telephone']);
			$this->native_session->set('schooladdress__addressline', $school['addressline']);
			$this->native_session->set('schooladdress__district', $school['district']);
			$this->native_session->set('schooladdress__country', $school['country']);
			if(!empty($school['county'])) $this->native_session->set('schooladdress__county', $school['county']);
		}
	}
	
	
	
	#Set the current school details
	function get_current()
	{
		return $this->_query_reader->get_row_as_array('get_teacher_jobs', array('user_id'=>$this->native_session->get('__user_id'), 'search_condition'=>" AND P.posting_end_date = '0000-00-00' LIMIT 1" ));
	}
	
	
	
	#Set the previous school details
	function get_previous()
	{
		return $this->_query_reader->get_list('get_previous_schools', array('user_id'=>$this->native_session->get('__user_id') ));
	}
	
	
	
		
	
	# Get list of schools
	function get_list($instructions=array())
	{
		$searchString = " S.status='verified' ";
		if(!empty($instructions['action']) && $instructions['action']== 'verify')
		{
			$searchString = " 1=1 "; #Show all schools
		}
		
		# If a search phrase is sent in the instructions
		if(!empty($instructions['searchstring']))
		{
			$searchString .= " AND ".$instructions['searchstring'];
		}
		
		# Instructions
		$count = !empty($instructions['pagecount'])? $instructions['pagecount']: NUM_OF_ROWS_PER_PAGE;
		$start = !empty($instructions['page'])? ($instructions['page']-1)*$count: 0;
		
		return $this->_query_reader->get_list('get_school_list_data',array('search_query'=>$searchString, 'limit_text'=>$start.','.($count+1), 'order_by'=>" S.last_updated DESC, S.date_added DESC "));
	}
	
	
	
	
	
	
	
	# Approve or reject a school
	function verify($instructions)
	{
		$result = array('boolean'=>false, 'msg'=>'ERROR: The school instructions could not be resolved.');
		
		if(!empty($instructions['action']))
		{
			switch($instructions['action'])
			{
				case 'approve':
					$result['boolean'] = $this->change_status($instructions['id'], 'verified');
				break;
				
				case 'reject':
					$result['boolean'] = $this->reject($instructions['id'],(!empty($instructions['reason'])? htmlentities($instructions['reason'], ENT_QUOTES): 'NONE'));
				break;
				
				case 'archive':
					$result['boolean'] = $this->change_status($instructions['id'], 'inactive');
				break;
				
				case 'restore':
					$result['boolean'] = $this->change_status($instructions['id'], 'pending');
				break;
			}
			
			if(!empty($result['boolean'])) 
			{
				$result['msg'] = $result['boolean']? "The user status has been changed": "ERROR: The user status could not be changed.";
			}
		}
		
		return $result;
	}
	
	
	
	

	
	# Change the status of the school
	function change_status($schoolId, $newStatus)
	{
		$school = $this->_query_reader->get_row_as_array('get_school_by_id', array('school_id'=>$schoolId));
		$result1 = !in_array($newStatus, array('pending'))? 
			$this->_messenger->send($school['added_by'], array('code'=>'notify_change_of_data_status', 'item'=>'school', 'details'=>"Name: ".$school['name']." <br>Telephone: ".$school['telephone']." <br>Location: ".$school['addressline']." ".$school['county']." ".$school['district'].", ".$school['country'], 'status'=>strtoupper($newStatus), 'approver_name'=>($this->native_session->get('__last_name').' '.$this->native_session->get('__first_name')), 'action_date'=>date('d-M-Y h:ia T', strtotime('now')) ))
			: true;
		
		$result2 = $this->_query_reader->run('update_item_status', array('item_id'=>$schoolId, 'table_name'=>'institution', 'status'=>$newStatus, 'updated_by'=>$this->native_session->get('__user_id') ));
		
		return get_decision(array($result1,$result2), FALSE);
	}
	
	
	
	
	
	# Reject a school application
	function reject($schoolId, $reason)
	{
		$school = $this->_query_reader->get_row_as_array('get_school_by_id', array('school_id'=>$schoolId));
		$result1 = $this->_messenger->send($school['added_by'], array('code'=>'notify_change_of_data_status', 'item'=>'school', 'details'=>"REASON FOR REJECTION:<br> ".$reason."<br>Please resubmit with reasons fixed.<br><br>Name: ".$school['name']." <br>Telephone: ".$school['telephone']." <br>Location: ".$school['addressline']." ".$school['county']." ".$school['district'].", ".$school['country'], 'status'=>'REJECTED', 'approver_name'=>($this->native_session->get('__last_name').' '.$this->native_session->get('__first_name')), 'action_date'=>date('d-M-Y h:ia T', strtotime('now')) ));
		
		$result2 = $this->_query_reader->run('delete_school_data', array('school_id'=>$schoolId));
		
		return get_decision(array($result1,$result2), FALSE);
	}
}


?>