<?php
/**
 * This class creates and manages teacher data.
 *
 * @author Al Zziwa <azziwa@gmail.com>
 * @version 1.1.0
 * @copyright TMIS
 * @created 01/30/2015
 */
class _teacher extends CI_Model
{
	#Constructor to set some default values at class load
	public function __construct()
    {
        parent::__construct();
		$this->load->model('_approval_chain');
	}
	
	
	# Add a new teacher
	function add_new($teacherDetails)
	{
		$msg = "";
		# First check if a user with the given email already exists
		$check = $this->_query_reader->get_row_as_array('get_user_by_email', array('email_address'=>$this->native_session->get('emailaddress') ));
		if(empty($check))
		{
			# 1. Add the person data
			$personId = $this->_query_reader->add_data('add_person_data', array('first_name'=>$this->native_session->get('firstname'), 'last_name'=>$this->native_session->get('lastname'), 'gender'=>$this->native_session->get('gender'), 'citizenship_country'=>$this->native_session->get('citizenship__country'), 'citizenship_type'=>$this->native_session->get('citizenship__citizentype'), 'marital_status'=>$this->native_session->get('marital'), 'date_of_birth'=>format_date($this->native_session->get('birthday'), 'YYYY-MM-DD') )); 
			
			if(!empty($personId) || $personId == 0)
			{
				$emailContactId = $this->_query_reader->add_data('add_contact_data', array('contact_type'=>'email', 'carrier_id'=>'', 'details'=>$this->native_session->get('emailaddress'), 'parent_id'=>$personId, 'parent_type'=>'person'));
				if($this->native_session->get('telephone')) 
				{
					$phoneContactId = $this->_query_reader->add_data('add_contact_data', array('contact_type'=>'telephone', 'carrier_id'=>'', 'details'=>$this->native_session->get('telephone'), 'parent_id'=>$personId, 'parent_type'=>'person'));
				}
						
				# Save the birth place
				if($this->native_session->get('birthplace__addressline'))
				{
					$birthPlaceId = $this->add_address($personId, array('address_type'=>'physical', 'importance'=>'birthplace', 'details'=>htmlentities($this->native_session->get('birthplace__addressline'), ENT_QUOTES), 'district'=>$this->native_session->get('birthplace__district'), 'country'=>$this->native_session->get('birthplace__country'), 'county'=>($this->native_session->get('birthplace__county')? $this->native_session->get('birthplace__county'): "") ));
				}
				# Save permanent address
				if($this->native_session->get('permanentaddress__addressline'))
				{
					$permanentId = $this->add_address($personId, array('address_type'=>$this->native_session->get('permanentaddress__addresstype'), 'importance'=>'permanent', 'details'=>$this->native_session->get('permanentaddress__addressline'), 'district'=>$this->native_session->get('permanentaddress__district'), 'country'=>$this->native_session->get('permanentaddress__country'), 'county'=>($this->native_session->get('permanentaddress__county')? $this->native_session->get('permanentaddress__county'): "") ));
				}
				# Save contact address
				if($this->native_session->get('contactaddress__addressline'))
				{
					$contactId = $this->add_address($personId, array('address_type'=>$this->native_session->get('contactaddress__addresstype'), 'importance'=>'contact', 'details'=>$this->native_session->get('contactaddress__addressline'), 'district'=>$this->native_session->get('contactaddress__district'), 'country'=>$this->native_session->get('contactaddress__country'), 'county'=>($this->native_session->get('contactaddress__county')? $this->native_session->get('contactaddress__county'): "") ));
				}
				
				if($this->native_session->get('teacherid'))
				{
					$this->add_another_id($personId, array('id_type'=>'teacher_id', 'id_value'=>$this->native_session->get('teacherid')));
				}
				
				# 3. Create an account - For the first account, use the user's email address as the login username.
				$password = generate_temp_password();
				$userId = $this->_query_reader->add_data('add_user_data', array('person_id'=>$personId, 'login_name'=>$this->native_session->get('emailaddress'), 'login_password'=>sha1($password), 'permission_group'=>'Teacher', 'status'=>'completed' ));
				
				if(empty($userId)) 
				{
					$msg = "ERROR: We could not create your user record.";
				}
				else
				{
					$result = $this->_messenger->send($userId, array('code'=>'introduce_new_user', 'email_from'=>SITE_ADMIN_MAIL, 'from_name'=>SITE_ADMIN_NAME, 'password'=>$password, 'first_name'=>$this->native_session->get('firstname'), 'emailaddress'=>$this->native_session->get('emailaddress'), 'login_link'=>base_url(), array('email')));
					if(!$result) $msg = "ERROR: We could not send the new account email message to the user.";
					
					$this->native_session->set('user_id', $userId);
				}
				
				
				# 4. Take note of the new teacher's person ID for use later
				if(!empty($result) && $result) 
				{
					$this->native_session->set('person_id', $personId);
					# If we have been using a temp person ID, update the database with the real ID and remove the temp one
					if($this->native_session->get('temp_person_id'))
					{
						$result = $this->_query_reader->run('update_document_owner', array('temp_id'=>$this->native_session->get('temp_person_id'), 'actual_id'=>$personId ));
						if($result) $this->native_session->delete('temp_person_id');
					}
				}
			}
			else
			{
				$msg = "ERROR: We could not create your record.";	
			}
		}
		else 
		{
			$msg = "WARNING: A user with the email entered already exists. Please login or recover your password to continue.";
		}
		
		return array('boolean'=>(!empty($result)? $result: false), 'msg'=>$msg, 'id'=>(!empty($userId)? $userId: ''), 'person_id'=>(!empty($personId)? $personId: ''));
	}
		
			
		
		
	# Update the teacher data
	function update($details)
	{
		$msg = '';
		$results = array();
		$user = $this->_query_reader->get_row_as_array('get_user_by_id', array('user_id'=>$details['userid']));
		
		$result = $this->_query_reader->run('update_person_data', array('person_id'=>$user['person_id'], 'first_name'=>$this->native_session->get('firstname'), 'last_name'=>$this->native_session->get('lastname'), 'gender'=>$this->native_session->get('gender'), 'marital_status'=>($this->native_session->get('marital')? $this->native_session->get('marital'): 'unknown'), 'citizenship_country'=>$this->native_session->get('citizenship__country'), 'citizenship_type'=>$this->native_session->get('citizenship__citizentype'), 'date_of_birth'=>format_date($this->native_session->get('birthday'), 'YYYY-MM-DD') ));  
		
		array_push($results, $result);
		
		if(!empty($details['telephone'])) 
		{
			$result = $this->_query_reader->run('update_contact_data', array('contact_type'=>'telephone', 'carrier_id'=>'', 'details'=>$details['telephone'], 'parent_id'=>$user['person_id'], 'parent_type'=>'person'));
			array_push($results, $result);
		}
		
		if($this->native_session->get('birthplace__addressline'))
		{
			$result = $this->_query_reader->run('update_address_data', array('parent_id'=>$user['person_id'], 'parent_type'=>'person', 'address_type'=>'physical', 'importance'=>'birthplace', 'details'=>$this->native_session->get('birthplace__addressline'), 'district'=>$this->native_session->get('birthplace__district'), 'country'=>$this->native_session->get('birthplace__country'), 'county'=>($this->native_session->get('birthplace__county')? $this->native_session->get('birthplace__county'): "") ));
			array_push($results, $result);
		}
		# Save permanent address
		if($this->native_session->get('permanentaddress__addressline'))
		{
			$result = $this->_query_reader->run('update_address_data', array('parent_id'=>$user['person_id'], 'parent_type'=>'person', 'address_type'=>$this->native_session->get('permanentaddress__addresstype'), 'importance'=>'permanent', 'details'=>$this->native_session->get('permanentaddress__addressline'), 'district'=>$this->native_session->get('permanentaddress__district'), 'country'=>$this->native_session->get('permanentaddress__country'), 'county'=>($this->native_session->get('permanentaddress__county')? $this->native_session->get('permanentaddress__county'): "") ));
		}
		# Save contact address
		if($this->native_session->get('contactaddress__addressline'))
		{
			$result = $this->_query_reader->run('update_address_data', array('parent_id'=>$user['person_id'], 'parent_type'=>'person', 'address_type'=>$this->native_session->get('contactaddress__addresstype'), 'importance'=>'contact', 'details'=>$this->native_session->get('contactaddress__addressline'), 'district'=>$this->native_session->get('contactaddress__district'), 'country'=>$this->native_session->get('contactaddress__country'), 'county'=>($this->native_session->get('contactaddress__county')? $this->native_session->get('contactaddress__county'): "") ));
		}
		
		
		$this->add_another_id($user['person_id'], array('id_type'=>'teacher_id', 'id_value'=>$this->native_session->get('teacherid')));
		
		return array('boolean'=>(!empty($result)? $result: false), 'msg'=>$msg, 'id'=>(!empty($userId)? $userId: ''), 'person_id'=>$user['person_id']);
	}
	
		
	
	
	# Add another ID that identifies that person on a third party system
	function add_another_id($personId, $idDetails)
	{
		return $this->_query_reader->add_data('add_another_id', array('parent_id'=>$personId, 'parent_type'=>'person', 'id_type'=>$idDetails['id_type'], 'id_value'=>$idDetails['id_value']));
	}		
		
	
	# Add a person's address
	function add_address($personId, $addressDetails)
	{
		return $this->_query_reader->add_data('add_new_address', array('parent_id'=>$personId, 'parent_type'=>'person', 'address_type'=>$addressDetails['address_type'], 'importance'=>$addressDetails['importance'], 'details'=>$addressDetails['details'], 'county'=>$addressDetails['county'], 'district'=>$addressDetails['district'], 'country'=>$addressDetails['country']));
	}	


	# Clear a teacher session profile
	function clear_session()
	{
		$fields = array('lastname'=>'', 'firstname'=>'', 'telephone'=>'', 'emailaddress'=>'', 'gender'=>'', 'marital'=>'', 'birthday'=>'', 'birthplace__addressline'=>'', 'birthplace__county'=>'', 'birthplace__district'=>'', 'birthplace__country'=>'', 'birthplace__addresstype'=>'', 'birthplace__district__hidden'=>'', 'birthplace__country__hidden'=>'', 'teacherid'=>'', 'permanentaddress'=>'', 'permanentaddress__addressline'=>'', 'permanentaddress__county'=>'', 'permanentaddress__district'=>'', 'permanentaddress__country'=>'', 'permanentaddress__addresstype'=>'', 'contactaddress'=>'', 'contactaddress__addressline'=>'', 'contactaddress__county'=>'', 'contactaddress__district'=>'', 'contactaddress__country'=>'', 'contactaddress__addresstype'=>'', 'citizenship__country'=>'', 'citizenship__citizentype'=>'', 'education_list'=>'', 'subject_list'=>'', 'is_admin_adding_teacher'=>'', 'profile_id'=>'', 'profile_personid'=>'', 'profile_loginname'=>'', 'profile_userrole'=>'', 'profile_lastname'=>'', 'profile_firstname'=>'', 'profile_signature'=>'', 'profile_telephone'=>'', 'profile_emailaddress'=>'', 'profile_photo'=>'');
		
		$this->native_session->delete_all($fields);
	}
	
	
	
	

	# Populate a user session profile
	function populate_session($teacherId)
	{
		$teacher = $this->_query_reader->get_row_as_array('get_teacher_profile', array('teacher_id'=>$teacherId));
		
		if(!empty($teacher))
		{
			$this->native_session->set('user_id',$teacherId);
			$this->native_session->set('lastname',$teacher['last_name']);
			$this->native_session->set('firstname',$teacher['first_name']);
			$this->native_session->set('telephone',$teacher['telephone']);
			$this->native_session->set('emailaddress',$teacher['email_address']);
			$this->native_session->set('gender',$teacher['gender']);
			$this->native_session->set('marital',$teacher['marital_status']);
			$this->native_session->set('birthday',$teacher['date_of_birth']);
			$this->native_session->set('photo',$teacher['photo']);
			$this->native_session->set('birthplace__addressline', $teacher['birthplace__addressline']);
			$this->native_session->set('birthplace__county',$teacher['birthplace__county']);
			$this->native_session->set('birthplace__district',$teacher['birthplace__district']);
			$this->native_session->set('birthplace__country',$teacher['birthplace__country']);
			$this->native_session->set('birthplace__addresstype',$teacher['birthplace__addresstype']);
			$this->native_session->set('teacherid',$teacher['teacher_id']);
			$this->native_session->set('person_id',$teacher['person_id']);
			$this->native_session->set('permanentaddress',$teacher['permanentaddress__addressline']);
			$this->native_session->set('permanentaddress__addressline',$teacher['permanentaddress__addressline']);
			$this->native_session->set('permanentaddress__county',$teacher['permanentaddress__county']);
			$this->native_session->set('permanentaddress__district',$teacher['permanentaddress__district']);
			$this->native_session->set('permanentaddress__country',$teacher['permanentaddress__country']);
			$this->native_session->set('permanentaddress__addresstype',$teacher['permanentaddress__addresstype']);
			$this->native_session->set('contactaddress',$teacher['contactaddress__addressline']);
			$this->native_session->set('contactaddress__addressline',$teacher['contactaddress__addressline']);
			$this->native_session->set('contactaddress__county',$teacher['contactaddress__county']);
			$this->native_session->set('contactaddress__district',$teacher['contactaddress__district']);
			$this->native_session->set('contactaddress__country',$teacher['contactaddress__country']);
			$this->native_session->set('contactaddress__addresstype',$teacher['contactaddress__addresstype']);
			$this->native_session->set('citizenship__country',$teacher['citizenship__country']);
			$this->native_session->set('citizenship__citizentype',$teacher['citizenship__type']);
			$this->native_session->set('verificationcode', generate_person_code($teacher['person_id']));
			#Get teacher education
			$education = $this->_query_reader->get_list('get_teacher_education', array('person_id'=>$teacher['person_id']));
			$this->native_session->set('education_list',$education);
			
			#Get teacher's subjects taught list
			$subjects = $this->_query_reader->get_list('get_teacher_subjects', array('person_id'=>$teacher['person_id']));
			$this->native_session->set('subject_list',$subjects);
			
			#Get teacher's document list
			$documents = $this->_query_reader->get_list('get_teacher_documents', array('person_id'=>$teacher['person_id']));
			$this->native_session->set('document_list',$documents);
		}
	}
	
				
		
		
	# Add new posted data to the session
	function add_to_session($details)
	{
		return process_fields($this, $details);
	}
	


		
	
	# Get list of teachers
	function get_list($instructions=array())
	{
		$searchString = " U.teacher_status='active' ";
		$query = 'get_teacher_list_data';
		if(!empty($instructions['action']) && $instructions['action']== 'view')
		{
			$searchString = " U.teacher_status IN ('pending', 'completed') ";
		}
		else if(!empty($instructions['action']) && $instructions['action']== 'verify')
		{
			$searchString = " U.teacher_status = 'completed' ";
		}
		else if(!empty($instructions['action']) && $instructions['action']== 'approve')
		{
			$searchString = " U.teacher_status = 'approved' ";
		}
		else if(!empty($instructions['action']) && $instructions['action']== 'payrollreport')
		{
			$searchString = " U.teacher_status NOT IN ('unknown','pending') ";
			$query = 'get_teacher_payroll_data';
		}
		else if(!empty($instructions['action']) && $instructions['action']== 'cimreport')
		{
			$searchString = " U.teacher_status NOT IN ('unknown','pending') ";
			$query = 'get_teacher_cim_data';
		}
		
		# If a search phrase is sent in the instructions
		if(!empty($instructions['searchstring']))
		{
			$searchString .= " AND ".$instructions['searchstring'];
		}
		
		# Instructions
		$count = !empty($instructions['pagecount'])? $instructions['pagecount']: NUM_OF_ROWS_PER_PAGE;
		$start = !empty($instructions['page'])? ($instructions['page']-1)*$count: 0;
		
		return $this->_query_reader->get_list($query, array('search_query'=>$searchString, 'limit_text'=>$start.','.($count+1), 'order_by'=>" ORDER BY U.last_updated DESC, U.date_added DESC "));
	}
	
	
	
	
	
	
	
	
	# Approve or reject a teacher
	function verify($instructions)
	{
		$result = array('boolean'=>false, 'msg'=>'ERROR: The teacher verification instructions could not be resolved.');
		
		if(!empty($instructions['action']))
		{
			switch($instructions['action'])
			{
				case 'approve_toapproved':
					$result = $this->_approval_chain->add_chain($instructions['id'], 'registration', '2', 'approved', (!empty($instructions['reason'])? htmlentities($instructions['reason'], ENT_QUOTES): '') ); 
					$this->change_status($instructions['id'], 'approved');
					
					#Also activate the user so that they can log in - at the first approval
					$result2 = $this->_query_reader->run('update_user_status', array('user_id'=>$instructions['id'], 'status'=>'active', 'updated_by'=>$this->native_session->get('__user_id') ));
				break;
				
				case 'reject_fromcompleted':
					$this->change_status($instructions['id'], 'pending');
				break;
				
				case 'approve_toactive':
					#These details are passed in the other field from the UI
					$instructions = process_other_field($instructions);
					$details['effectivedate'] = $instructions['effectivedate'];
					$details['grade__grades'] = $instructions['grade__grades'];
					#print_r($details['grade__grades']);
					#exit();
					
					$result = $this->_approval_chain->add_chain($instructions['id'], 'registration', '3', 'approved', (!empty($instructions['reason'])? htmlentities($instructions['reason'], ENT_QUOTES): ''), $details);
					
					$this->change_status($instructions['id'], 'active');
				break;
				
				case 'reject_fromapproved':
					$this->change_status($instructions['id'], 'completed');
				break;
				
				case 'archive':
					$result = $this->change_status($instructions['id'], 'archived');
				break;
				
				case 'restore':
					$result = $this->change_status($instructions['id'], 'active');
				break;
			}
		}
		
		return $result;
	}
	
	
	
	
	
	

	
	# Change the status of the teacher
	function change_status($userId, $newStatus)
	{
		$result1 = !in_array($newStatus, array('archived'))? $this->_messenger->send($userId, array('code'=>'notify_change_of_user_status', 'status'=>strtoupper($newStatus)) ): true;
		
		$result2 = $this->_query_reader->run('update_teacher_status', array('user_id'=>$userId, 'status'=>$newStatus, 'updated_by'=>$this->native_session->get('__user_id') ));
		
		return get_decision(array($result1,$result2), FALSE);
	}
	
	
	
	
	# Get teacher documents
	function get_documents($teacherId)
	{
		return $this->_query_reader->get_list('get_user_documents', array('user_id'=>$teacherId));
	}
	
	
	
	# Generate custom report
	function generate_custom_report($specs)
	{
		$list = array();
		if($specs['reporttype__reporttypes'] == 'Number of Registered Teachers')
		{
			switch($specs['reportsubtype__registerednumbers'])
			{
				case 'By Gender':
					$list = $this->_query_reader->get_list('report_stat_registerednumbers_by_gender');
				break;
				
				case 'By Grade':
					$list = $this->_query_reader->get_list('report_stat_registerednumbers_by_grade');
				break;
				
				case 'By Subject':
					$list = $this->_query_reader->get_list('report_stat_registerednumbers_by_subject');
				break;
				
				case 'By School':
					$list = $this->_query_reader->get_list('report_stat_registerednumbers_by_school');
				break;
				
				case 'By District':
					$list = $this->_query_reader->get_list('report_stat_registerednumbers_by_district');
				break;
				
				case 'By Region':
					$list = $this->_query_reader->get_list('report_stat_registerednumbers_by_region');
				break;
			}
			
		}
		else if($specs['reporttype__reporttypes'] == 'Teacher Appointments')
		{
			switch($specs['reportsubtype__teacherappointments'])
			{
				case 'By Post':
					$list = $this->_query_reader->get_list('report_stat_teacherappointments_by_post', array('start_date'=>format_date($specs['startdate']),'end_date'=>format_date($specs['enddate']) ));
				break;
				
				case 'By School':
					$list = $this->_query_reader->get_list('report_stat_teacherappointments_by_school', array('start_date'=>format_date($specs['startdate']),'end_date'=>format_date($specs['enddate']) ));
				break;
				
				case 'By District':
					$list = $this->_query_reader->get_list('report_stat_teacherappointments_by_district', array('start_date'=>format_date($specs['startdate']),'end_date'=>format_date($specs['enddate']) ));
				break;
				
				case 'By Region':
					$list = $this->_query_reader->get_list('report_stat_teacherappointments_by_region', array('start_date'=>format_date($specs['startdate']),'end_date'=>format_date($specs['enddate']) ));
				break;
			}
		}
		else if($specs['reporttype__reporttypes'] == 'Teacher Status')
		{
			switch($specs['reportsubtype__teacherstatus'])
			{
				case 'Applied':
					$list = $this->_query_reader->get_list('report_stat_teacherstatus_applied');
				break;
				
				case 'On Probation':
					$list = $this->_query_reader->get_list('report_stat_teacherstatus_on_probation');
				break;
				
				case 'Confirmed':
					$list = $this->_query_reader->get_list('report_stat_teacherstatus_confirmed');
				break;
				
				case 'On Leave':
					$list = $this->_query_reader->get_list('report_stat_teacherstatus_on_leave');
				break;
				
				case 'Retired':
					$list = $this->_query_reader->get_list('report_stat_teacherstatus_retired');
				break;
			}
		}
		
		return $list;
	}
	
}


?>