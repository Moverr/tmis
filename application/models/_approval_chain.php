<?php
/**
 * This class manages the approval processes in the system.
 *
 * @author Al Zziwa <azziwa@gmail.com>
 * @version 1.1.0
 * @copyright TMIS
 * @created 01/08/2015
 */
class _approval_chain extends CI_Model
{
	
	# Add a new approval chain
	function add_chain($subjectId, $chainType, $step, $status, $comment='', $actionDetails=array())
	{
		$msg = "ERROR: The approval could not be recorded.";
		
		# Get the user id to use
		if($chainType == 'registration')
		{
			$thisUserId = !$this->native_session->get('__user_id')? $this->native_session->get('user_id'): $subjectId;
		}
		else $thisUserId = $this->native_session->get('__user_id');
		
		
		# 1. Record the approval chain
		# 1. a) Get the originator from the previous chain
		if($step != '1')
		{
			$chain = $this->_query_reader->get_row_as_array('get_step_chain', array('subject_id'=>$subjectId, 'step_number'=>($step - 1), 'chain_type'=>$chainType));
			$originatorId = $chain['actual_approver'];
		}
		else
		{
			$originatorId = '';
		}
		
		 
		# The next approvers
		if(empty($originatorId) || (!empty($chain) && $chain['max_steps'] != $step)) 
			$approvers = $this->get_next_approver((!empty($originatorId)? $originatorId: $thisUserId), $chainType, $step);
		else if(!empty($chain) && $chain['max_steps'] == $step) $approvers = 'COMPLETE';
		
		# 1. b) Add the approval chain if there are approvers
		if(!empty($approvers)) 
		{
			$approverString = ($approvers == 'COMPLETE')? '': implode(',',$approvers);
			
			$chainId = $this->_query_reader->add_data('add_approval_chain', array('chain_type'=>$chainType, 'step_number'=>$step, 'subject_id'=>$subjectId, 'originator_id'=>$originatorId, 'actual_approver'=>$thisUserId, 'status'=>$status, 'approver_id'=>$approverString, 'comment'=>$comment )); 
		}
		
		# 2. Perform action
		if(!empty($chainId)) 
		{
			$result = $this->action($chainId, $actionDetails);
			if($result) $msg = "Your approval actions have been successful";
		}
		
		# 3. Determine what message to send
		if(empty($approvers)) $msg = "ERROR: No approvers could be found for the next stage.";
		else if(empty($chainId) && $status == 'approved') $msg = "ERROR: The chain could not be committed.";
		else if(!empty($result) && !$result) $msg = "ERROR: The approval actions could not be executed.";
		
		return array('boolean'=>(!empty($result) && $result? true: false), 'msg'=>$msg);
	}
			
	
	
	# Get the action to perform based on the step in the chain.
	function action($chainId, $otherDetails=array())
	{
		#print_r($otherDetails);
		#exit();
		# 1. Get the chain details and settings
		$chain = $this->_query_reader->get_row_as_array('get_approval_chain_by_id', array('chain_id'=>$chainId));
		if(!empty($chain)) $settings = $this->_query_reader->get_row_as_array('get_approval_chain_setting', array('chain_type'=>$chain['chain_type'], 'step_number'=>$chain['step_number']));
		
		# 2. Get all the actions to be perfomed from the code
		if(!empty($settings['step_actions'])) $actions = explode(',',$settings['step_actions']);
		
		# 3. Combine actions to determine the result to return
		$results = array();
		if(!empty($actions)) 
		{
			foreach($actions AS $action)
			{
				switch($action)
				{
					case 'notify_next_chain_party':
						$result = $this->notify_next_chain_party($chain);
					break;
					
					case 'notify_previous_and_next_chain_parties':
						$result = $this->notify_previous_and_next_chain_parties($chain);
					break;
					
					case 'publish_job_notice':
						$result = $this->publish_job_notice($chain);
					break;
					
					case 'notify_previous_chain_parties':
						$result = $this->notify_previous_chain_parties($chain);
					break;
					
					case 'issue_confirmation_letter':
					//issue confirmatin letter :: 
						$result = $this->issue_confirmation_letter($chain, $otherDetails);
					break;
					
					case 'issue_file_number':
						$result = $this->issue_file_number($chain);
					break;
					
					case 'issue_registration_certificate':
						$result = $this->issue_registration_certificate($chain, $otherDetails);
					break;
					
					case 'issue_transfer_letter':
						$result = $this->issue_transfer_letter($chain, $otherDetails);
					break;
					
					case 'submit_transfer_pca':
						$result = $this->submit_transfer_pca($chain, $otherDetails);
					break;
					
					case 'confirm_transfer':
						$result = $this->confirm_transfer($chain);
					break;
					
					case 'send_verification_letter':
						$result = $this->send_verification_letter($chain, $otherDetails);
					break;
					
					case 'confirm_retirement':
						$result = $this->confirm_retirement($chain, $otherDetails);
					break;
					
					case 'apply_data_secrecy':
						$result = $this->apply_data_secrecy($chain);
					break;
					
					case 'activate_data_records':
						$result = $this->activate_data_records($chain);
					break;
					
					case 'change_teacher_posting':
						$result = $this->change_teacher_posting($chain);
					break;
					
					default:
						$result = false;
					break;
				}
				array_push($results, $result);
			}
		}
		
		return get_decision($results, FALSE);
	}
	
	
	
	# Notify the next party in an approval chain
	function notify_next_chain_party($chain)
	{
		$results = array();
		$approvers = array_unique(explode(",", $chain['approver_id']));
		
		foreach($approvers AS $approverId)
		{
			$result = $this->_messenger->send($approverId, array('code'=>'notify_next_chain_party', 'item_type'=>$chain['chain_type'], 'originator_name'=>$chain['originator_name'], 'action_date'=>date('d-M-Y H:i:sAT', strtotime('now')), 'email_from'=>SITE_ADMIN_MAIL, 'from_name'=>SITE_ADMIN_NAME ));
			array_push($results,$result); 
		}
		
		return get_decision($results);
	}
	
	
	# Notify the previous and next party in an approval chain
	function notify_previous_and_next_chain_parties($chain)
	{
		return  $this->notify_previous_chain_parties($chain) && $this->notify_next_chain_party($chain);
	}	
	
	
	
	# Get previous parties in the approval chain
	function get_previous_parties($chain)
	{
		return $this->_query_reader->get_single_column_as_array('get_parties_in_chain', 'actual_approver', array('subject_id'=>$chain['subject_id'], 'chain_type'=>$chain['chain_type']));
	}
	
	
	
	
	# Notify the previous chain parties
	function notify_previous_chain_parties($chain)
	{
		$results = array();
		# 1. Get first originator and previous approvers
		$parties = $this->get_previous_parties($chain);
		
		# 2. Notify all parties 
		foreach($parties AS $approverId)
		{
			$result = $this->_messenger->send($approverId, array('code'=>'notify_previous_chain_parties', 'item_type'=>$chain['chain_type'], 'originator_name'=>$chain['originator_name'], 'approver_name'=>$chain['approver_name'], 'verification_result'=>$chain['status'], 'action_date'=>date('d-M-Y H:i:sAT', strtotime('now')), 'email_from'=>SITE_ADMIN_MAIL, 'from_name'=>SITE_ADMIN_NAME ));
			array_push($results,$result); 
		}
		
		return get_decision($results, FALSE);
	}
	
	
	# Publish job notice as part of the approval process
	function publish_job_notice($chain)
	{
		return $this->_query_reader->run('update_vacancy_status', array('vacancy_id'=>$chain['subject_id'], 'status'=>'published'));
	}

	
	
	#Send a document as part of the approval process
	function send_document($chain, $documentType, $requiredModes=array('system'), $otherDetails=array())
	{
		//grade_grades changes to Teacher_grades :: 
		echo 'send_document';
		print_r($otherDetails);
		echo "<br/> :::: <br/>";
		#exit();
		$this->load->model('_document');
		$originator = $this->_query_reader->get_row_as_array('get_originator_of_chain', array('subject_id'=>$chain['subject_id'], 'chain_type'=>$chain['chain_type']));
		
		if(!empty($originator['originator']))
		{
			# Generate a PDF of the confirmation letter and send it to the confirmed teacher.
			$details = $this->_query_reader->get_row_as_array('get_user_profile', array('user_id'=>$originator['originator']));
			if(!empty($otherDetails)) $details = array_merge($details, $otherDetails);
			
			//adding the grade to the flow of te application 
			$grade = '';
			$grade_category = $this->native_session->get('graded');
			if(!empty($grade_category) && $grade_category == 'true')
			{
				
				 $doc_code = str_replace(' ', '_', $otherDetails['teacher_grade']);		
				
			 
			
			}
			else
			{
				$doc_code = 'document__'.$documentType.$grade;
			}
			
			# print_r($doc_code); exit();	 
			
		#	graded
		 #	print_r('document__'.$documentType.$grade); exit();
			#Generate the letter PDF
			$letterUrl = $this->_document->generate_letter($doc_code, $details);
			print_r($letterUrl);
			exit();
			
			# Send the document to the user's email and originator of the approval process if they are different in their system
			if(!empty($letterUrl))
			{
				$result = $this->_messenger->send($originator['originator'], array('code'=>'send_'.$documentType, 'fileurl'=>UPLOAD_DIRECTORY.'documents/'.$letterUrl, 'email_from'=>SITE_ADMIN_MAIL, 'from_name'=>SITE_ADMIN_NAME), $requiredModes);
				
				# Record the document for future tracking
				if($result) $this->_query_reader->run('add_system_document', array('url'=>$letterUrl, 'document_type'=>$documentType, 'tracking_number'=>$details['tracking_number'], 'description'=>ucwords(str_replace('_', ' ', $documentType)), 'parent_id'=>$details['person_id'], 'parent_type'=>'person' ));
				
				# Copy the actual approver if they are different
				if(!empty($chain['actual_approver']) && $chain['actual_approver'] != $originator['originator']) $this->_messenger->send($chain['actual_approver'], array('code'=>'send_'.$documentType, 'fileurl'=>UPLOAD_DIRECTORY.'documents/'.$letterUrl, 'email_from'=>SITE_ADMIN_MAIL, 'from_name'=>SITE_ADMIN_NAME), $requiredModes);
				
				return $result;
			}
		}
		else
		{
			return false;
		}
	}

	
	# Issue a file number to the user as part of the approval process
	function issue_file_number($chain)
	{
		$originator = $this->_query_reader->get_row_as_array('get_originator_of_chain', array('subject_id'=>$chain['subject_id'], 'chain_type'=>$chain['chain_type']));
		
		$user = $this->_query_reader->get_row_as_array('get_user_profile', array('user_id'=>$originator['originator']));
		$fileNumber = 'UTS'.sprintf("%'.07d\n",$user['person_id']);
		
		#Record the file number on the teacher's person profile
		$result1 = $this->_query_reader->run('update_person_profile_part', array('person_id'=>$user['person_id'], 'query_part'=>" file_number='".$fileNumber."' "));
		
		# Make this a bonafide teacher user
		$result2 = $this->_query_reader->run('update_user_permission_group', array('permission_group'=>'Teacher', 'updated_by'=>$this->native_session->get('__user_id'), 'user_id'=>$originator['originator']));
		
		# Disable editing on the teacher's documents
		$result3 = $this->_query_reader->run('disable_document_editing', array('person_id'=>$user['person_id']));
		
		return $result1 && $result2 && $result3 && $this->_messenger->send($originator['originator'], array('code'=>'issue_file_number', 'first_name'=>$user['first_name'], 'file_number'=>$fileNumber, 'email_from'=>SITE_ADMIN_MAIL, 'from_name'=>SITE_ADMIN_NAME));
	}
	
	
	
	# Issue confirmation letter as part of the approval process
	function issue_confirmation_letter($chain, $otherDetails)
	{
		$originator = $this->_query_reader->get_row_as_array('get_originator_of_chain', array('subject_id'=>$chain['subject_id'], 'chain_type'=>$chain['chain_type']));
		$user = $this->_query_reader->get_row_as_array('get_user_profile', array('user_id'=>$originator['originator']));
		$approver = $this->_query_reader->get_row_as_array('get_user_profile', array('user_id'=>$this->native_session->get('__user_id')));
		$job = $this->_query_reader->get_row_as_array('get_teacher_jobs', array('user_id'=>$originator['originator'], 'search_condition'=>" AND P.id='".$chain['subject_id']."' "));
		
		$actionDetails['date_today'] = date('jS F Y', strtotime('now'));
		$actionDetails['asset_folder'] = BASE_URL."assets/";
		$actionDetails['document_size'] = 'A4';
		$actionDetails['document_orientation'] = 'portrait';
		$actionDetails['teacher_name'] = ucfirst($user['last_name'].', '.$user['first_name']);
		$actionDetails['new_position'] = $job['job'];
		$actionDetails['school_name'] = $job['school'];
		$actionDetails['school_address'] = $job['addressline'].' '.$job['county'].' '.$job['district'].', '.$job['country'];
		$actionDetails['tracking_number'] = $this->generate_tracking_number('confirmation');
		$actionDetails['tracking_image'] = "<img src='".base_url()."external_libraries/phpqrcode/qr_code.php?value=".$actionDetails['tracking_number']."' />";
		$actionDetails['minute_number'] = $otherDetails['minutenumber'];
		$actionDetails['approver_signature'] = $approver['signature'];
		$actionDetails['approver_name'] = ucfirst($approver['last_name'].', '.$approver['first_name']);
		
		return $this->send_document($chain, 'confirmation_letter', array('system'), $actionDetails);
	}
	
	
	
	# Generate a tracking number for a letter
	function generate_tracking_number($letterType)
	{
		$letterCodes = array('confirmation'=>'51', 'pca'=>'52', 'transfer'=>'53', 'leave'=>'54', 'retirement'=>'55');
		return $letterCodes[$letterType].strtotime('now');
	}
	
	
	
	
	# Issue registration certificate as part of the approval process
	function issue_registration_certificate($chain, $otherDetails)
	{
		#echo 'issue_registration_certificate';
		#print_r($otherDetails); exit();
		$user = $this->_query_reader->get_row_as_array('get_user_profile', array('user_id'=>$chain['subject_id']));
		
		$actionDetails['date_today'] = date('d-M-Y', strtotime('now'));
		$actionDetails['asset_folder'] = BASE_URL."assets/";
		$actionDetails['document_size'] = 'A4';
		$actionDetails['document_orientation'] = 'landscape';
		$actionDetails['teacher_name'] = strtoupper($user['last_name'].', '.$user['first_name']);
		$actionDetails['teacher_grade'] = strtoupper($otherDetails['grade__grades']);
		$actionDetails['effective_date'] = date('d-M-Y', strtotime($otherDetails['effectivedate']));
		$actionDetails['certificate_number'] = $this->generate_certificate_number($chain['subject_id'], $otherDetails['grade__grades']);
		$actionDetails['tracking_number'] = $actionDetails['certificate_number'];
		$approver = $this->_query_reader->get_row_as_array('get_user_profile', array('user_id'=>$this->native_session->get('__user_id')));
		$actionDetails['signature_url'] = $approver['signature'];
	 
	 	if(!empty($this -> native_session->get('grade')))
		$actionDetails['grade'] = $this->native_session->get('grade');
		
		return !empty($actionDetails['certificate_number'])? $this->send_document($chain, 'registration_certificate', array('system'), $actionDetails): false;
	}
	
	
	
	# Generate a certificate number for a teacher
	function generate_certificate_number($teacherId, $grade)
	{
		$numberStart = $this->_query_reader->get_row_as_array('get_grade_details_by_name', array('grade_name'=>$grade));
		$number = $numberStart['number'].strtotime('now');
		#record the new certificate for the teacher
		$result = $this->_query_reader->run('add_another_id', array('parent_id'=>$teacherId, 'parent_type'=>'user', 'id_type'=>'certificate_number', 'id_value'=>$number));
		
		return $result? $number: '';
	}
	
	
	
	# Issue transfer letter as part of the approval process
	function issue_transfer_letter($chain, $otherDetails)
	{
		$transfer = $this->_query_reader->get_row_as_array('get_transfer_list_data', array('search_query'=>" T.id='".$chain['subject_id']."' ", 'order_by'=>' T.last_updated DESC ', 'limit_text'=>'1'));
		$currentPosting = $this->_query_reader->get_row_as_array('get_job_postings', array('search_query'=>" P.postee_id='".$transfer['teacher_id']."' AND P.posting_end_date='0000-00-00' ", 'order_by'=>' P.last_updated DESC ', 'limit_text'=>'1'));
		
		$approver = $this->_query_reader->get_row_as_array('get_user_profile', array('user_id'=>$this->native_session->get('__user_id')));
		
		$actionDetails['date_today'] = date('jS F Y', strtotime('now'));
		$actionDetails['asset_folder'] = BASE_URL."assets/";
		$actionDetails['document_size'] = 'A4';
		$actionDetails['document_orientation'] = 'portrait';
		$actionDetails['teacher_name'] = ucfirst($currentPosting['teacher_name']);
		$actionDetails['file_number'] = $currentPosting['file_number'];
		$actionDetails['old_position'] = $transfer['old_job'];
		$actionDetails['new_position'] = $currentPosting['job'];
		$actionDetails['school_name'] = $transfer['desired_school_name'];
		$actionDetails['school_address'] = $transfer['addressline'].' '.$transfer['county'].' '.$transfer['district'].', '.$transfer['country'];
		$actionDetails['tracking_number'] = $this->generate_tracking_number('transfer');
		$actionDetails['tracking_image'] = "<img src='".base_url()."external_libraries/phpqrcode/qr_code.php?value=".$actionDetails['tracking_number']."' />";
		$actionDetails['minute_number'] = $otherDetails['minutenumber'];
		$actionDetails['approver_signature'] = $approver['signature'];
		$actionDetails['approver_name'] = ucfirst($approver['last_name'].', '.$approver['first_name']);
		
		return $this->send_document($chain, 'transfer_letter', array('system'), $actionDetails);
	}
	
	
	# Submit transfer PCA as part of the approval process
	function submit_transfer_pca($chain, $otherDetails)
	{
		$transfer = $this->_query_reader->get_row_as_array('get_transfer_list_data', array('search_query'=>" T.id='".$chain['subject_id']."' ", 'order_by'=>' T.last_updated DESC ', 'limit_text'=>'1'));
		$currentPosting = $this->_query_reader->get_row_as_array('get_job_postings', array('search_query'=>" P.postee_id='".$transfer['teacher_id']."' AND P.posting_end_date='0000-00-00' ", 'order_by'=>' P.last_updated DESC ', 'limit_text'=>'1'));
		
		$approver = $this->_query_reader->get_row_as_array('get_user_profile', array('user_id'=>$this->native_session->get('__user_id')));
		
		$actionDetails['date_today'] = date('jS F Y', strtotime('now'));
		$actionDetails['date_year'] = date('Y', strtotime('now'));
		$actionDetails['asset_folder'] = BASE_URL."assets/";
		$actionDetails['document_size'] = 'A4';
		$actionDetails['document_orientation'] = 'portrait';
		$actionDetails['teacher_name'] = ucfirst($currentPosting['teacher_name']);
		$actionDetails['file_number'] = $currentPosting['file_number'];
		
		$actionDetails['old_school_name'] = $transfer['current_school_name'];
		$actionDetails['subject_list'] = $otherDetails['subjectlist'];
		$actionDetails['new_school_name'] = $transfer['desired_school_name'];
		
		$actionDetails['tracking_number'] = $this->generate_tracking_number('pca');
		$actionDetails['tracking_image'] = "<img src='".base_url()."external_libraries/phpqrcode/qr_code.php?value=".$actionDetails['tracking_number']."' />";
		$actionDetails['approver_signature'] = $approver['signature'];
		$actionDetails['approver_name'] = ucfirst($approver['last_name'].', '.$approver['first_name']);
		
		return $this->send_document($chain, 'transfer_pca', array('system'), $actionDetails);
	}
	
	
	# Send verification letter as part of the approval process
	function send_verification_letter($chain, $otherDetails)
	{
		$leave = $this->_query_reader->get_row_as_array('get_leave_list_data', array('search_query'=>" L.id='".$chain['subject_id']."' ", 'order_by'=>' L.last_updated DESC ', 'limit_text'=>'1'));
		
		$approver = $this->_query_reader->get_row_as_array('get_user_profile', array('user_id'=>$this->native_session->get('__user_id')));
		
		$actionDetails['date_today'] = date('jS F Y', strtotime('now'));
		$actionDetails['asset_folder'] = BASE_URL."assets/";
		$actionDetails['document_size'] = 'A4';
		$actionDetails['document_orientation'] = 'portrait';
		$actionDetails['teacher_name'] = ucfirst($leave['teacher_name']);
		$actionDetails['job_position'] = $leave['job'];
		$actionDetails['school_name'] = $leave['school_name'];
		$actionDetails['school_address'] = $leave['addressline'].' '.$leave['county'].' '.$leave['district'].', '.$leave['country'];
		$actionDetails['tracking_number'] = $this->generate_tracking_number('leave');
		$actionDetails['tracking_image'] = "<img src='".base_url()."external_libraries/phpqrcode/qr_code.php?value=".$actionDetails['tracking_number']."' />";
		$actionDetails['leave_type'] = strtoupper($otherDetails['leavetype']);
		$actionDetails['leavesmall_type'] = strtolower($otherDetails['leavetype']);
		$actionDetails['minute_number'] = $otherDetails['minutenumber'];
		$actionDetails['start_date'] = date('d/m/Y', strtotime($otherDetails['startdate']));
		$actionDetails['end_date'] = date('d/m/Y', strtotime($otherDetails['enddate']));
		$actionDetails['leave_reason'] = $otherDetails['leavereason'];
		$actionDetails['approver_signature'] = $approver['signature'];
		$actionDetails['approver_name'] = ucfirst($approver['last_name'].', '.$approver['first_name']);
		
		return $this->send_document($chain, 'verification_letter', array('system'), $actionDetails);
	}
	
	
	# Confirm retirement as part of the approval process
	function confirm_retirement($chain, $otherDetails)
	{
		$retirement = $this->_query_reader->get_row_as_array('get_retirement_list_data', array('search_query'=>" R.retiree_id='".$chain['subject_id']."' ", 'order_by'=>' R.last_updated DESC ', 'limit_text'=>'1'));
		
		$approver = $this->_query_reader->get_row_as_array('get_user_profile', array('user_id'=>$this->native_session->get('__user_id')));
		
		$actionDetails['date_today'] = date('jS F Y', strtotime('now'));
		$actionDetails['asset_folder'] = BASE_URL."assets/";
		$actionDetails['document_size'] = 'A4';
		$actionDetails['document_orientation'] = 'portrait';
		$actionDetails['teacher_name'] = strtoupper($retirement['teacher_name']);
		$actionDetails['job_position'] = strtoupper($retirement['job']);
		$actionDetails['file_number'] = $retirement['file_number'];
		$actionDetails['tracking_number'] = $this->generate_tracking_number('retirement');
		$actionDetails['tracking_image'] = "<img src='".base_url()."external_libraries/phpqrcode/qr_code.php?value=".$actionDetails['tracking_number']."' />";
		$actionDetails['retirement_details'] = $otherDetails['reason'];
		$actionDetails['approver_signature'] = $approver['signature'];
		$actionDetails['approver_name'] = ucfirst($approver['last_name'].', '.$approver['first_name']);
		
		# 1. Send retirement letter
		$result1 = $this->send_document($chain, 'retirement_letter', array('email'), $actionDetails);
		# 2. Update status of the person's user accounts to inactive
		$result2 = $result1? $this->_query_reader->run('deactivate_user_profile', array('user_id'=>$retirement['retiree_id'])): false;
		
		return get_decision(array($result1, $result2));
	}
	
	
	# Confirm transfer as part of the approval process
	function confirm_transfer($chain, $otherDetails=array())
	{
		$currentPosting = $this->get_current_posting_from_chain($chain);
		return !empty($currentPosting['id'])? $this->_query_reader->run('change_posting_field', array('posting_id'=>$currentPosting['id'], 'field_name'=>'status', 'field_value'=>'verified', 'updated_by'=>$this->native_session->get('__user_id') )): false;
	}
	
	
	# Apply data secrecy as part of the approval process
	function apply_data_secrecy($chain)
	{
		return $this->_query_reader->run('update_profile_visibility', array('is_visible'=>'N', 'person_id'=>$chain['subject_id']));
	}
	
	
	# Activate data records as part of the approval process
	function activate_data_records($chain)
	{
		# Get the data records scope from the subject id
		# scope in the format: [record type]|[id list]
		$scope = explode('|', $chain['subject_id']);
		
		# Take action based on the record type
		switch($scope[0])
		{
			case 'teacher':
				$result = $this->_query_reader->run('activate_teacher_data', array('updated_by'=>$this->native_session->get('__user_id'), 'id_list'=>"'".implode("','", explode(',', $scope[1]))."'" ));
			break;
			
			case 'school':
				$idList = "'".implode("','", explode(',', $scope[1]))."'";
				$result = $this->_query_reader->run('activate_school_data', array('updated_by'=>$this->native_session->get('__user_id'), 'id_list'=>$idList ));
				
				if($result) 
				{
					$schools = $this->_query_reader->get_list('get_schools_with_ids', array('id_list'=>$idList));
					foreach($schools AS $school) $this->_query_reader->run('add_approved_school_data', array('institution_name'=>$school['name'], 'start_date'=>$school['date_registered'], 'added_by'=>$this->native_session->get('__user_id') ));
				}
			break;
			
			case 'census':
				$result = $this->_query_reader->run('activate_census_data', array('updated_by'=>$this->native_session->get('__user_id'), 'id_list'=>"'".implode("','", explode(',', $scope[1]))."'" ));
			break;
		}
		
		return $result;
	}
	
			
	
	
	# Get the next approver in the chain stage
	function get_next_approver($originatorId, $chainType, $stepNumber)
	{
		$approvers = array();
		# 1. Get the chain settings
		$chainSetttings = $this->_query_reader->get_row_as_array('get_approval_chain_setting', array('chain_type'=>$chainType, 'step_number'=>($stepNumber+1) ));
		
		# 2. Get the scope details of the originator to properly obtain the next approver scope
		$originator = $this->_query_reader->get_row_as_array('get_originator_scope', array('originator_id'=>$originatorId));
	
		# 3. Now extract the approvers list
		if(!empty($chainSetttings['approvers']))
		{
			$scopes = $this->_query_reader->get_list('get_approver_scope', array('group_list'=>"'".implode("','", explode(',', $chainSetttings['approvers']))."'"));
			
			# 2. Get the users within that scope
			foreach($scopes AS $scope)
			{
				if($scope['scope'] == 'institution')
				{
					$condition = " AND PT.institution_id IN ('".implode("','",explode(',',$originator['institutions']))."') ";
				}
				else if($scope['scope'] == 'county')
				{
					$condition = " AND A.county IN ('".implode("','",explode(',',$originator['counties']))."') ";
				}
				else if($scope['scope'] == 'district')
				{
					$condition = " AND A.district_id IN ('".implode("','",explode(',',$originator['districts']))."') ";
				}
				# Take all users at that scope - for now (as TMIS is for only Uganda)
				else if($scope['scope'] == 'country')
				{
					$condition = "";
				}
				# Take all users at that scope
				else if($scope['scope'] == 'system')
				{
					$condition = "";
				}
				
				# Proceed with users who are allowed to approve
				if(!empty($scope['scope']) && $scope['scope'] != 'self')
				{
					$approvers = array_merge($approvers, $this->_query_reader->get_single_column_as_array('get_users_in_group', 'user_id', array('group'=>$scope['approver'], 'condition'=>$condition)) );
				}
			}
		}
		
		return array_unique($approvers);
	}	





	# Change the posting of the teacher to a new school
	# This fucntion is called when effecting a transfer
	function change_teacher_posting($chain)
	{
		# 1. End the previous teacher assignment
		$transfer = $this->_query_reader->get_row_as_array('get_transfer_list_data', array('search_query'=>" T.id='".$chain['subject_id']."' ", 'order_by'=>' T.last_updated DESC ', 'limit_text'=>'1'));
		$currentPosting = $this->_query_reader->get_row_as_array('get_job_postings', array('search_query'=>" P.postee_id='".$transfer['teacher_id']."' AND P.posting_end_date='0000-00-00' ", 'order_by'=>' P.last_updated DESC ', 'limit_text'=>'1'));
		
		if(!empty($currentPosting)) 
			$result1 = $this->_query_reader->run('terminate_teacher_posting', array('posting_id'=>$currentPosting['id'], 'updated_by'=>$this->native_session->get('__user_id') ));
		else $result1 = false;
		
		# 2. Create a new teacher posting for the new assignment
		if($result1)
			$result2 = $this->_query_reader->run('add_plain_posting', array('postee_id'=>$transfer['teacher_id'], 'institution_id'=>$transfer['new_school_id'], 'notes'=>'-- TRANSFERRED FROM: '.$currentPosting['institution_name'].' ('.$currentPosting['job'].') --', 'role_id'=>$currentPosting['role_id'], 'final_interview_id'=>$currentPosting['final_interview_id'], 'status'=>'pending', 'added_by'=>$this->native_session->get('__user_id') ));
		else $result2 = false;
		
		return get_decision(array($result1, $result2));
	}
	
	
	
	# Get current posting from chain
	function get_current_posting_from_chain($chain)
	{
		# 1. Get the transfer details
		$transfer = $this->_query_reader->get_row_as_array('get_transfer_list_data', array('search_query'=>" T.id='".$chain['subject_id']."' ", 'order_by'=>' T.last_updated DESC ', 'limit_text'=>'1'));
		
		# 2. Get the current assignment from the transfer details on the teacher
		return $this->_query_reader->get_row_as_array('get_job_postings', array('search_query'=>" P.postee_id='".$transfer['teacher_id']."' AND P.posting_end_date='0000-00-00' ", 'order_by'=>' P.last_updated DESC ', 'limit_text'=>'1'));
	}


	

		
	
	# Get list of approvals
	function get_list($instructions=array())
	{
		$searchString = " 1=1 ";
		
		# If a search phrase is sent in the instructions
		if(!empty($instructions['searchstring']))
		{
			$searchString .= " AND ".$instructions['searchstring'];
		}
		
		# Instructions
		$count = !empty($instructions['pagecount'])? $instructions['pagecount']: NUM_OF_ROWS_PER_PAGE;
		$start = !empty($instructions['page'])? ($instructions['page']-1)*$count: 0;
		
		return $this->_query_reader->get_list('get_approval_list_data',array('search_query'=>$searchString, 'limit_text'=>$start.','.($count+1), 'order_by'=>" A.last_updated DESC "));
	}
	
	
	
	
	
	
	
}


?>