<?php
/**
 * This class manages addition, removal and update of the person in the system.
 *
 * @author Al Zziwa <azziwa@gmail.com>
 * @version 1.1.0
 * @copyright TMIS
 * @created 01/08/2015
 */
class _person extends CI_Model
{
	#Constructor to set some default values at class load
	public function __construct()
    {
        parent::__construct();
		$this->load->helper('form');
	}
	
	# Add a person's profile
	function add_profile($profileDetails)
	{
		#Check if this is an admin adding the profile
		$isAdmin = check_access($this, 'add_new_user', 'boolean');
		
		$required = array('firstname', 'lastname', 'emailaddress');
		if(!$isAdmin && $this->native_session->get('__user_id')) array_push($required, 'gender', 'marital', 'birthday', 'birthplace'); 
		
		# 1. Add all provided data into the session
		#If the user is editing, they may not be given the email address, let us add it
		$profileDetails['emailaddress'] = !empty($profileDetails['emailaddress'])? $profileDetails['emailaddress']: ($this->native_session->get('emailaddress')? $this->native_session->get('emailaddress'): '');
		$passed = process_fields($this, $profileDetails, $required, array("/"));
		$msg = !empty($passed['msg'])? $passed['msg']: "";
			
		# 2. Save the data into the database
		if($passed['boolean'])
		{
			$details = $passed['data'];
			
			# First check if a user with the given email already exists
			$check = $this->_query_reader->get_row_as_array('get_user_by_email', array('email_address'=>$details['emailaddress']));
			if((empty($check) && !$this->native_session->get('person_id')) || ($this->native_session->get('person_id') && !empty($check)))
			{
				# Determine whether to update the data or to create a new record
				if($this->native_session->get('person_id'))
				{
					$updateResult = $this->update_profile($this->native_session->get('person_id'), $details);
					$result = $updateResult['boolean'];
					$msg = $updateResult['msg'];
				}
				else
				{
					$details['gender'] = !empty($details['gender'])? $details['gender']: 'unknown';
					$details['birthday'] = !empty($details['birthday'])? format_date($details['birthday'], 'YYYY-MM-DD'): '0000-00-00';
					
					$personId = $this->_query_reader->add_data('add_person_data', array('first_name'=>$details['firstname'], 'last_name'=>$details['lastname'], 'gender'=>$details['gender'], 'citizenship_country'=>(!empty($details['citizenship__country'])? $details['citizenship__country']: ''), 'citizenship_type'=>(!empty($details['citizenship__citizentype'])?$details['citizenship__citizentype']: '') , 'marital_status'=>$details['marital'], 'date_of_birth'=>$details['birthday'] )); 
			
					if(!empty($personId) || $personId == 0)
					{
						$emailContactId = $this->_query_reader->add_data('add_contact_data', array('contact_type'=>'email', 'carrier_id'=>'', 'details'=>$details['emailaddress'], 'parent_id'=>$personId, 'parent_type'=>'person'));
						if(!empty($details['telephone'])) 
						{
							$phoneContactId = $this->_query_reader->add_data('add_contact_data', array('contact_type'=>'telephone', 'carrier_id'=>'', 'details'=>$details['telephone'], 'parent_id'=>$personId, 'parent_type'=>'person'));
						}
						
						# Save the birth place
						if($this->native_session->get('birthplace__addressline'))
						{
							$birthPlaceId = $this->add_address($personId, array('address_type'=>'physical', 'importance'=>'birthplace', 'details'=>htmlentities($this->native_session->get('birthplace__addressline'), ENT_QUOTES), 'district'=>$this->native_session->get('birthplace__district'), 'country'=>$this->native_session->get('birthplace__country'), 'county'=>($this->native_session->get('birthplace__county')? $this->native_session->get('birthplace__county'): "") ));
						}
		
						# 3. Create an account and generate a confirmation code
						# For the first account, use the user's email address as the login username.
						$password = generate_temp_password();
						$userId = $this->_query_reader->add_data('add_user_data', array('person_id'=>$personId, 'login_name'=>$details['emailaddress'], 'login_password'=>sha1($password), 'permission_group'=>(!empty($details['role__roles'])? $details['role__roles']: 'Teacher Applicant'), 'status'=>($isAdmin || !empty($details['step1']) || !empty($details['step2'])? 'active':'completed') ));
						if(empty($userId)) 
						{
							$msg = "ERROR: We could not create your user record.";
						}
						else
						{
							$this->native_session->set('user_id', $userId);
						}
					
						# 4. Send confirmation code to contacts
						if($isAdmin)
						{
							$result = $this->_messenger->send($userId, array('code'=>'introduce_new_user', 'email_from'=>SITE_ADMIN_MAIL, 'from_name'=>SITE_ADMIN_NAME, 'password'=>$password, 'first_name'=>htmlentities($details['firstname'], ENT_QUOTES), 'emailaddress'=>$details['emailaddress'], 'login_link'=>base_url() ), array('email'));
							if(!$result) $msg = "ERROR: We could not send the new account email message to the user.";
						}
						#This is a teacher
						else if(!empty($details['step1']) || !empty($details['step2']))
						{
							$this->_query_reader->run('update_teacher_status', array('user_id'=>$userId, 'status'=>'pending', 'updated_by'=>$userId));
							
							$code = generate_person_code($personId);
							$result = $this->_messenger->send($userId, array('code'=>'new_teacher_first_step', 'email_from'=>SIGNUP_EMAIL, 'from_name'=>SITE_GENERAL_NAME, 'verification_code'=>$code, 'password'=>$password, 'first_name'=>htmlentities($details['firstname'], ENT_QUOTES), 'emailaddress'=>$details['emailaddress'], 'login_link'=>base_url() ), array('email'));
							if(!$result) 
							{
								#Delete the saved record if any, since the user has no access to their account either way
								$this->_query_reader->run('delete_user_data', array('user_id'=>$userId));
								$msg = "ERROR: We could not send the email message with your code.";
							}
						}
						# This is a normal application
						else
						{
							$result = $this->_messenger->send($userId, array('code'=>'introduce_new_user', 'email_from'=>SITE_ADMIN_MAIL, 'from_name'=>SITE_ADMIN_NAME, 'password'=>$password."<br>This password will not be active until your application has been approved.<br>We will notify you by this same email when your account has been approved.", 'first_name'=>htmlentities($details['firstname'], ENT_QUOTES), 'emailaddress'=>$details['emailaddress'], 'login_link'=>base_url() ), array('email'));
							
							if(!$result) $msg = "ERROR: We could not send the new account email message to your registered emailaddress.";
						}
						
						if($result) $this->native_session->set('person_id', $personId);
					}
					else
					{
						$msg = "ERROR: We could not create your record.";	
					}
				}
			} 
			else 
			{
				$msg = "WARNING: A user with the email entered already exists. Please login or recover your password to continue.";
			}
		} 
		else
		{
			$msg = !empty($msg)? $msg: "WARNING: Some required fields were left empty or entered with invalid characters. Please recheck your data and submit.";
		}
		
		return array('boolean'=>(!empty($result)? $result: false), 'msg'=>$msg);
	}
	
	
	
	# Add a person's address
	function add_address($personId, $addressDetails)
	{
		return $this->_query_reader->add_data('add_new_address', array('parent_id'=>$personId, 'parent_type'=>'person', 'address_type'=>$addressDetails['address_type'], 'importance'=>$addressDetails['importance'], 'details'=>$addressDetails['details'], 'county'=>$addressDetails['county'], 'district'=>$addressDetails['district'], 'country'=>$addressDetails['country']));
	}	
		
	
	
	# Update the person's profile
	function update_profile($personId, $details)
	{
		$results = array();
		$details['gender'] = !empty($details['gender'])? $details['gender']: 'unknown';
		$details['marital'] = !empty($details['marital'])? $details['marital']: 'unknown';
		$details['birthday'] = !empty($details['birthday'])? format_date($details['birthday'], 'YYYY-MM-DD'): '0000-00-00';
					
		$result = $this->_query_reader->run('update_person_data', array('person_id'=>$personId, 'first_name'=>htmlentities($details['firstname'], ENT_QUOTES), 'last_name'=>htmlentities($details['lastname'], ENT_QUOTES), 'marital_status'=>$details['marital'], 'gender'=>$details['gender'], 'date_of_birth'=>$details['birthday'] )); 
		array_push($results, $result);
		
		if(!empty($details['telephone'])) 
		{
			$result = $this->_query_reader->run('update_contact_data', array('contact_type'=>'telephone', 'carrier_id'=>'', 'details'=>$details['telephone'], 'parent_id'=>$personId, 'parent_type'=>'person'));
			array_push($results, $result);
		}
		
		if($this->native_session->get('birthplace__addressline'))
		{
			$result = $this->_query_reader->run('update_address_data', array('parent_id'=>$personId, 'parent_type'=>'person', 'address_type'=>'physical', 'importance'=>'birthplace', 'details'=>htmlentities($this->native_session->get('birthplace__addressline'), ENT_QUOTES), 'district'=>$this->native_session->get('birthplace__district'), 'country'=>$this->native_session->get('birthplace__country'), 'county'=>($this->native_session->get('birthplace__county')? $this->native_session->get('birthplace__county'): "") ));
			array_push($results, $result);
		}
		
		$isUpdated = get_decision($results);
		
		return array('boolean'=>$isUpdated, 'msg'=>($isUpdated? 'The profile has been updated.': 'ERROR: The profile could not be updated.'));
	}	
	
	
	
	# Add identification and contact addresses for the person
	function add_id_and_contacts($personId, $addressDetails)
	{
		$this->load->model('_validator');
		$msg = "";
		$required = array('verificationcode', 'permanentaddress', 'contactaddress', 'citizenship__country', 'citizenship__citizentype');
		
		# 1. Add all provided data into the session
		$passed = process_fields($this, $addressDetails, $required, array("/"));
		
		if($passed['boolean'])
		{
			$details = $passed['data'];
			
			# Determine whether to update the data or to create a new record
			if($this->native_session->get('edit_step_2'))
			{
				$updateResult = $this->update_id_and_contacts($personId, $details);
				$result = $updateResult['boolean'];
				$msg = $updateResult['msg'];
			}
			# New record
			else
			{
				# 2. Verify the confirmation code
				if($this->_validator->is_valid_confirmation_code($personId, $details['verificationcode']))
				{
					# Activate teacher user ID
					$result = $this->_query_reader->run('activate_teacher_applicant_user', array('person_id'=>$personId));
					
					# 3. Save all the data into the database
					if(!empty($details['teacherid'])) 
					{
						$this->add_another_id($personId, array('id_type'=>'teacher_id', 'id_value'=>htmlentities(restore_bad_chars($details['teacherid']), ENT_QUOTES) ));
					}
					#Save permanent and contact addresses
					$permanentId = $this->add_address($personId, array('address_type'=>$this->native_session->get('permanentaddress__addresstype'), 'importance'=>'permanent', 'details'=>htmlentities($this->native_session->get('permanentaddress__addressline'), ENT_QUOTES), 'district'=>$this->native_session->get('permanentaddress__district'), 'country'=>$this->native_session->get('permanentaddress__country'), 'county'=>($this->native_session->get('permanentaddress__county')? $this->native_session->get('permanentaddress__county'): "") ));
					$contactId = $this->add_address($personId, array('address_type'=>$this->native_session->get('contactaddress__addresstype'), 'importance'=>'contact', 'details'=>htmlentities($this->native_session->get('contactaddress__addressline'), ENT_QUOTES), 'district'=>$this->native_session->get('contactaddress__district'), 'country'=>$this->native_session->get('contactaddress__country'), 'county'=>($this->native_session->get('contactaddress__county')? $this->native_session->get('contactaddress__county'): "") ));
				
					#Save the citizenship information
					$result = $this->_query_reader->run('update_person_citizenship', array('person_id'=>$personId, 'citizen_country'=>htmlentities($details['citizenship__country'], ENT_QUOTES), 'citizenship_type'=>$details['citizenship__citizentype'] ));
				
					if($result) {
						$this->native_session->set('edit_step_2', 'Y');
					} else {
						$msg = "ERROR: We could not save your information. Please try again.";
					}
				}
				else
				{
					$msg = "WARNING: The provided confirmation code is not valid. Please re-check your email message.";
				}
			}
		}
		else
		{
			$msg = "WARNING: Some required fields were left empty or entered with invalid characters. Please recheck your data and submit.";
		}
		
		# 4. Prepare appropriate message and return
		return array('boolean'=>(!empty($result)? $result: false), 'msg'=>$msg);
	}
	
		
	
	
	# Add another ID that identifies that person on a third party system
	function add_another_id($personId, $details)
	{
		return $this->_query_reader->add_data('add_another_id', array('parent_id'=>$personId, 'parent_type'=>'person', 'id_type'=>$details['id_type'], 'id_value'=>$details['id_value'] ));
	}	
	
	
	# Update the person's ID and contact details
	function update_id_and_contacts($personId, $details)
	{
		$results = array();
		
		if(!empty($details['teacherid'])) 
		{
			$result = $this->_query_reader->run('update_another_id', array('parent_id'=>$personId, 'parent_type'=>'person', 'id_type'=>'teacher_id', 'id_value'=>$details['teacherid']));
		}
		
		$result = $this->_query_reader->run('update_address_data', array('parent_id'=>$personId, 'parent_type'=>'person', 'address_type'=>$this->native_session->get('permanentaddress__addresstype'), 'importance'=>'permanent', 'details'=>htmlentities($this->native_session->get('permanentaddress__addressline'), ENT_QUOTES), 'district'=>$this->native_session->get('permanentaddress__district'), 'country'=>$this->native_session->get('permanentaddress__country'), 'county'=>($this->native_session->get('permanentaddress__county')? $this->native_session->get('permanentaddress__county'): "") ));
		array_push($results, $result);
		
		
		$result = $this->_query_reader->run('update_address_data', array('parent_id'=>$personId, 'parent_type'=>'person', 'address_type'=>$this->native_session->get('contactaddress__addresstype'), 'importance'=>'contact', 'details'=>htmlentities($this->native_session->get('contactaddress__addressline'), ENT_QUOTES), 'district'=>$this->native_session->get('contactaddress__district'), 'country'=>$this->native_session->get('contactaddress__country'), 'county'=>($this->native_session->get('contactaddress__county')? $this->native_session->get('contactaddress__county'): "") ));
		array_push($results, $result);
				
		#Save the citizenship information
		$result = $this->_query_reader->run('update_person_citizenship', array('person_id'=>$personId, 'citizen_country'=>htmlentities($details['citizenship__country'], ENT_QUOTES), 'citizenship_type'=>$details['citizenship__citizentype'] ));
		array_push($results, $result);
		
		$isUpdated = get_decision($results);
		
		return array('boolean'=>$isUpdated, 'msg'=>($isUpdated? 'Your ID and contact information has been updated.': 'ERROR: Your ID and contact information could not be updated.'));
	}	
	
		
	
	
	# Add the person's education
	function add_education($personId, $educationDetails)
	{
		$msg = "";
		$required = array('institutionname', 'institution__institutiontype', 'from__month', 'from__pastyear', 'to__month', 'to__pastyear', 'certificatename', 'certificatenumber');
		$isAdded = false;
		
		$passed = process_fields($this, $educationDetails, $required);
		
		if($passed['boolean'])
		{
			$details = $passed['data'];
			
			# Make sure the "to" is greater than the "from" date
			if((strtotime($details['to__month'].' 01, '.$details['to__pastyear']) - strtotime($details['from__month'].' 01, '.$details['from__pastyear'])) > 0)
			{
				# Determine whether to update the data or to create a new record
				if($this->native_session->get('edit_step_3_education') && !empty($details['education_id']))
				{
					$updateResult = $this->update_education($personId, $details);
					$isAdded = $updateResult['boolean'];
					$msg = $updateResult['msg'];
					$this->native_session->delete('edit_step_3_education');
				}
				# New record - add to the education session array
				else
				{
					$educationList = $this->native_session->get('education_list')? $this->native_session->get('education_list'): array();
					
					$education = array('institutionname'=>$details['institutionname'], 'institution__institutiontype'=>$details['institution__institutiontype'], 'from__month'=>$details['from__month'], 'from__pastyear'=>$details['from__pastyear'], 'to__month'=>$details['to__month'], 'to__pastyear'=>$details['to__pastyear'], 'certificatename'=>$details['certificatename'], 'certificatenumber'=>$details['certificatenumber'], 'highestcertificate'=>(!empty($details['highestcertificate'])? $details['highestcertificate']: ""), 'education_id'=>strtotime('now'));
					
					array_push($educationList, $education);
					$this->native_session->set('education_list', $educationList);
					$isAdded = true;
				}
			}
			else
			{
				$msg = "WARNING: The start date can not be equal to or greater than the end date.";
			}
		}
		
		return array('boolean'=>$isAdded, 'msg'=>(!empty($msg)? $msg: 'Your education information has been added.'));
	}	
		
	
		
	
	
	# Update the person's education
	function update_education($personId, $details)
	{
		$isUpdated = false;
		$list = $this->native_session->get('education_list');
		$position = get_row_from_list($list, 'education_id', $details['education_id'], 'key');
		
		if(!empty($position) || $position == 0)
		{
			$list[$position] = array('institutionname'=>$details['institutionname'], 'institution__institutiontype'=>$details['institution__institutiontype'], 'from__month'=>$details['from__month'], 'from__pastyear'=>$details['from__pastyear'], 'to__month'=>$details['to__month'], 'to__pastyear'=>$details['to__pastyear'], 'certificatename'=>$details['certificatename'], 'certificatenumber'=>$details['certificatenumber'], 'highestcertificate'=>(!empty($details['highestcertificate'])? $details['highestcertificate']: ""), 'education_id'=>$details['education_id']);
			
			$this->native_session->set('education_list', $list);
			$isUpdated = true;
		}
		
		return array('boolean'=>$isUpdated, 'msg'=>'Your education information has been updated.');
	}
	
	
	
	
	
	# Add a subject taught
	function add_subject_taught($personId, $subjectDetails)
	{
		$msg = "";
		$required = array('subjectname', 'subject__subjecttype');
		$isAdded = false;
		
		$passed = process_fields($this, $subjectDetails, $required);
		
		if($passed['boolean'])
		{
			$details = $passed['data'];
			
			# Determine whether to update the data or to create a new record
			if($this->native_session->get('edit_step_3_subject') && !empty($details['subject_id']))
			{
				$updateResult = $this->update_subject_taught($personId, $details);
				$isAdded = $updateResult['boolean'];
				$msg = $updateResult['msg'];
				$this->native_session->delete('edit_step_3_subject');
			}
			# New record - add to the subject session array
			else
			{
				$subjectList = $this->native_session->get('subject_list')? $this->native_session->get('subject_list'): array();
					
				$subject = array('subjectname'=>$details['subjectname'], 'subject__subjecttype'=>$details['subject__subjecttype'], 'subject_id'=>strtotime('now'));
					
				array_push($subjectList, $subject);
				$this->native_session->set('subject_list', $subjectList);
				$isAdded = true;
			}
		}
		
		return array('boolean'=>$isAdded, 'msg'=>(!empty($msg)? $msg: 'Your subject information has been added.'));
	}
		
	
	
	# Update the person's subjects taught
	function update_subject_taught($personId, $details)
	{
		$isUpdated = false;
		$list = $this->native_session->get('subject_list');
		$position = get_row_from_list($list, 'subject_id', $details['subject_id'], 'key');
		
		if(!empty($position) || $position == 0)
		{
			$list[$position] = array('subjectname'=>$details['subjectname'], 'subject__subjecttype'=>$details['subject__subjecttype'], 'subject_id'=>$details['subject_id']);
			
			$this->native_session->set('subject_list', $list);
			$isUpdated = true;
		}
		
		return array('boolean'=>$isUpdated, 'msg'=>'Your subject information has been updated.');
	}
	
	
	
	# Remove a list item
	function remove_list_item($listType, $itemId)
	{
		$result = false;
		$list = $this->native_session->get($listType.'_list');
		$key = get_row_from_list($list, $listType.'_id', $itemId, 'key');
		if(!empty($key) || $key == 0)
		{
			
			#if this is a document, delete the saved document too
			if($listType == 'document')
			{
				$result1 = $this->_query_reader->run('delete_user_document_by_url', array('url'=>$list[$key]['documenturl']));
				if($result1) @unlink(UPLOAD_DIRECTORY.'documents/'.$list[$key]['documenturl']);
			}
			
			unset($list[$key]); 
			$this->native_session->set($listType.'_list', $list);	
			$result = !empty($result1)? $result1: true;
		}
		
		return $result;
	}
	
	
	
	
	# Add education and qualifications
	function add_education_and_qualifications($personId, $details)
	{
		$isAdded = false;
		$results = array();
		
		# If the user is editing, first remove the old records
		if($this->native_session->get('edit_step_3') || !empty($personId))
		{
			$result1 = $this->_query_reader->run('remove_academic_history', array('person_id'=>$personId));
			$result2 = $this->_query_reader->run('remove_subject_data', array('parent_id'=>$personId, 'parent_type'=>'person'));
			array_push($results, $result1, $result2);
		}
		
		if($this->native_session->get('education_list')){
			foreach($this->native_session->get('education_list') AS $educationIndex=>$row)
			{
				$result = $this->_query_reader->run('add_academic_history', array('person_id'=>$personId, 'institution'=>$row['institutionname'], 'institution_type'=>$row['institution__institutiontype'], 'start_date'=>format_date($row['from__month'].' 01, '.$row['from__pastyear'], 'YYYY-MM-DD'), 'end_date'=>format_date($row['to__month'].' 01, '.$row['to__pastyear'], 'YYYY-MM-DD'), 'certificate_name'=>$row['certificatename'], 'certificate_number'=>$row['certificatenumber'], 'is_highest'=>(!empty($row['highestcertificate'])? 'Y': 'N'), 'added_by'=>($this->native_session->get('__user_id')? $this->native_session->get('__user_id'): $this->native_session->get('user_id')) ));
				
				array_push($results, $result);
			}
			
		}
		
		
		if($this->native_session->get('subject_list'))
		{
			foreach($this->native_session->get('subject_list') AS $subjectIndex=>$row)
			{
				$result = $this->_query_reader->run('add_subject_data', array('parent_id'=>$personId, 'parent_type'=>'person', 'details'=>$row['subjectname'], 'study_category'=>$row['subject__subjecttype'] ));
				
				array_push($results, $result);
			}
		}
		
		$isAdded = get_decision($results);
		
		
		# If this is a new record mark this as a start of the editing of the page
		if($isAdded && isset($educationIndex) && isset($subjectIndex) && !$this->native_session->get('edit_step_3')) 
		{
			$this->native_session->set('edit_step_3', 'Y');
		} 
		else if(!isset($educationIndex) || !isset($subjectIndex))
		{
			$isAdded = false;
			$msg = "WARNING: Your education and subjects are required to continue.";
		}
		
		
		# Prepare appropriate message to return
		$action = $this->native_session->get('edit_step_3')? "updated": "added";
		return array('boolean'=>$isAdded, 'msg'=>($isAdded? 'Your education and qualification information has been '.$action.'.': (!empty($msg)? $msg: 'ERROR: Your education and qualification information could not be '.$action.'.'))  );
	}
	
	
	
	
	# Submit the application
	function submit_application($personId, $details)
	{
		$msg = "";
		#if(!is_model_loaded($this, '_approval_chain')) 
		$this->load->model('_approval_chain');
		
		# 1. Mark the user status as complete - for the admin to be able to approve it
		$result1 = $this->_query_reader->run('update_teacher_status', array('user_id'=>$details['user_id'], 'status'=>'completed', 'updated_by'=>$details['user_id']));
		$result2 = $this->_query_reader->run('update_user_status', array('user_id'=>$details['user_id'], 'status'=>'active', 'updated_by'=>$this->native_session->get('__user_id') ));
		if(!$result1) $msg = "ERROR: We could not set up your user account for activation.";
		
		# 2. Start the teacher approval chain
		$result = $this->_approval_chain->add_chain($details['user_id'], 'registration', '1', 'approved');
		$result2 = $result['boolean'];
		if(!$result2) $msg = "ERROR: We could not set up your registration for approval.";
		
		# 3. Send notification of application submission
		$result3 =  $this->_messenger->send($details['user_id'], array('code'=>'teacher_application_submitted', 'email_from'=>SIGNUP_EMAIL, 'from_name'=>SITE_GENERAL_NAME, 'first_name'=>htmlentities($details['first_name'], ENT_QUOTES), 'emailaddress'=>$details['emailaddress'], 'login_link'=>base_url() ), array('email'));
		
		if(!$result3) $msg = "ERROR: We could not send your application confirmation email.";
		$isSubmitted = get_decision(array($result1,$result2), FALSE);
		
		return array('boolean'=>$isSubmitted, 'msg'=>($isSubmitted? "Your application has been submitted": $msg));
	}
	
	
		
	
	
	# Add a qualification document for this person
	function add_document($personId, $details)
	{
		$response = array('boolean'=>false, 'msg'=>'ERROR: We could not add the document.');
		
		# Determine whether to update the data or to create a new record
		# UPDATE
		if(!empty($details['document_id']) && !empty($details['documentname']))
		{
			$response['boolean'] = $this->_query_reader->run('update_document_field', array('document_id'=>$details['document_id'], 'field_name'=>'description', 'field_value'=>htmlentities($details['documentname'], ENT_QUOTES) ));
			# proceed to update the session if the db update was successful
			if($response['boolean']) 
			{
				$documentList = $this->native_session->get('document_list');
				$key = get_row_from_list($documentList, 'document_id', $details['document_id'], 'key');
				$documentList[$key]['documentname'] = $details['documentname'];
				
				$response['msg'] = 'The document has been updated.';
				$this->native_session->set('document_list', $documentList);
			}
			$this->native_session->delete('edit_step_3_document');
		}
		
		# ADD NEW
		else if(!empty($details['documenturl__fileurl']) && !empty($details['documentname']))
		{
			# Determine how to get a person ID if it is not given
			$currentStamp = strtotime('now');
			if(empty($personId))
			{
				if($this->native_session->get('person_id'))
				{
					$personId = $this->native_session->get('person_id');
				}
				# Make up a temporary person ID if it is not yet known (for single entry forms)
				else if(!$this->native_session->get('temp_person_id'))
				{
					$personId = 'TEMP-'.$this->native_session->get('__user_id').'-'.$currentStamp;
					$this->native_session->set('temp_person_id', $personId);
				}
				else $personId = $this->native_session->get('temp_person_id');
			}
				
			$documentId = $this->_query_reader->add_data('add_user_document', array('url'=>$details['documenturl__fileurl'],'document_type'=>'qualification_document','tracking_number'=>$currentStamp,'description'=>htmlentities($details['documentname'], ENT_QUOTES), 'is_removable'=>'Y','parent_id'=>$personId,'parent_type'=>'person'));
			
			if(!empty($documentId)) 
			{
				$documentList = $this->native_session->get('document_list')? $this->native_session->get('document_list'): array();
				array_push($documentList, array('document_id'=>$documentId, 'documenturl'=>$details['documenturl__fileurl'], 'documentname'=>$details['documentname']));
				$this->native_session->set('document_list', $documentList);
				
				$response = array('boolean'=>true, 'msg'=>'The document has been added.');
			}
		}
		
		return $response;
	}
	
	
	
	
}


?>