<?php
/**
 * This class handles user functions in the system.
 *
 * @author Al Zziwa <azziwa@gmail.com>
 * @version 1.1.0
 * @copyright TMIS
 * @created 01/08/2015
 */
class _user extends CI_Model
{
	# Verify a user
	function verify($instructions)
	{
		$result = array('boolean'=>false, 'msg'=>'ERROR: The user instructions could not be resolved.');
		
		if(!empty($instructions['action']))
		{
			switch($instructions['action'])
			{
				case 'approve':
					$result['boolean'] = $this->change_status($instructions['id'], 'active');
				break;
				
				case 'reject':
					$result['boolean'] = $this->reject($instructions['id'],(!empty($instructions['reason'])? htmlentities($instructions['reason'], ENT_QUOTES): 'NONE'));
				break;
				
				case 'block':
					$result['boolean'] = $this->change_status($instructions['id'], 'blocked');
				break;
				
				case 'archive':
					$result['boolean'] = $this->change_status($instructions['id'], 'archived');
				break;
				
				case 'restore':
					$result['boolean'] = $this->change_status($instructions['id'], 'completed');
				break;
			}
			
			if(!empty($result['boolean'])) 
			{
				$result['msg'] = $result['boolean']? "The user status has been changed": "ERROR: The user status could not be changed.";
			}
		}
		
		return $result;
	}
	
	
	

	
	# Change the status of the user
	function change_status($userId, $newStatus)
	{
		$result1 = !in_array($newStatus, array('archived','complete'))? $this->_messenger->send($userId, array('code'=>'notify_change_of_user_status', 'status'=>strtoupper($newStatus)), array('email')): true;
		$result2 = $this->_query_reader->run('update_user_status', array('user_id'=>$userId, 'status'=>$newStatus, 'updated_by'=>$this->native_session->get('__user_id') ));
		
		return get_decision(array($result1,$result2), FALSE);
	}
	
	
	# Reject a user application
	function reject($userId, $reason)
	{
		$result1 = $this->_messenger->send($userId, array('code'=>'reject_user_application', 'reason'=>$reason), array('email'));
		$result2 = $this->_query_reader->run('delete_user_data', array('user_id'=>$userId));
		
		return get_decision(array($result1,$result2), FALSE);
	}
	
	
	
	# Update a user account 
	function update($userId, $details)
	{
		$isUpdated = false;
		$msg = "";
		
		# Check if the user is changing their password
		if(!empty($details['currentpassword']) || !empty($details['newpassword']) || !empty($details['repeatpassword']))
		{
			# 1. If any of the above fields is empty, do not proceed to validate
			if(!empty($details['currentpassword']) && !empty($details['newpassword']) && !empty($details['repeatpassword']))
			{
				# 2. Check whether the new password is valid
				if($this->is_valid_password($details['newpassword']))
				{
					# 3. Check if the repeated password and the new password match
					if($details['newpassword'] == $details['repeatpassword'])
					{
						# 4. Now check whether the current password is valid
						$user = $this->_query_reader->get_row_as_array('get_user_by_name_and_pass', array('login_name'=>$this->native_session->get('profile_loginname'), 'login_password'=>sha1($details['currentpassword']) ));
						
						if(!empty($user))
						{
							$isUpdated = $this->_query_reader->run('update_user_password', array('new_password'=>sha1($details['newpassword']), 'old_password'=>sha1($details['currentpassword']), 'updated_by'=>$this->native_session->get('__user_id'), 'user_id'=>$this->native_session->get('profile_id') ));
							$msg = $isUpdated? "Your profile changes have been applied.": "ERROR: Your password could not be updated.";
						}
						else
						{
							$msg = "WARNING: The current password provided is not valid.";
						}
						
					}
					else
					{
						$msg = "WARNING: The new password and your repeated password do not match.";
					}
					
				}
				else
				{
					$msg = "WARNING: Your new password does not meet the minimum security requirements.";
				}
			}
			else
			{
				$msg = "WARNING: Please provide the current password and your new password.";
			}
		}
		
		
		# User is not changing password - OR - the password change was successfull
		if($isUpdated || (!$isUpdated && $msg == ''))
		{
			#Get which person id to use
			if(!empty($details['userid']))
			{
				$user = $this->_query_reader->get_row_as_array('get_user_profile', array('user_id'=>$details['userid']));
				$personId = $user['person_id'];
			}
			else
			{
				$user = $this->_query_reader->get_row_as_array('get_user_profile', array('user_id'=>$this->native_session->get('__user_id')));
				$personId = $this->native_session->get('__person_id');
			}
			
			# Update the person details
			$isUpdated = $this->_query_reader->run('update_person_profile_part', array('query_part'=>" first_name='".htmlentities($details['firstname'], ENT_QUOTES)."', last_name='".htmlentities($details['lastname'], ENT_QUOTES)."' ", 'person_id'=>$personId ));
			if(!$isUpdated)
			{
				$msg = "ERROR: We could not update the name.";
			}
			
			# Add or update the contact telephone if given
			else if(!empty($details['telephone']))
			{
				$queryCode = (!empty($user['telephone'])) || (empty($details['userid']) && $this->native_session->get('profile_telephone'))? 'update_contact_data': 'add_contact_data';
				
				$isUpdated = $this->_query_reader->run($queryCode, array('details'=>$details['telephone'], 'carrier_id'=>'', 'contact_type'=>'telephone', 'parent_type'=>'person', 'parent_id'=>$personId ));
				
				$msg = $isUpdated? "The updates have been applied.": "ERROR: We could not update the telephone.";
			}
			
			# A new signature file has been submitted
			if(!empty($details['signature']))
			{
				if(!empty($details['signature__fileurl']))
				{
					$isUpdated = $this->_query_reader->run('update_person_profile_part', array('query_part'=>" signature='".$details['signature__fileurl']."' ", 'person_id'=>$personId ));
					#Update some profile settings for the user in case they do not log out
					if($isUpdated)
					{
						$this->native_session->delete('__nosignature');
						$this->native_session->set('__signature', $details['signature__fileurl']);
						# Remove the old image
						$signature = UPLOAD_DIRECTORY.'images/'.$user['signature'];
						if(!empty($user['signature'])) if(file_exists($signature)) unlink($signature);
					}
				}
				else
				{
					$msg = "WARNING: The uploaded file format is not supported.";
				}
			}
			
			# A new photo file has been submitted
			if(!empty($details['photo']))
			{
				if(!empty($details['photo__fileurl']))
				{
					$isUpdated = $this->_query_reader->run('update_person_profile_part', array('query_part'=>" photo='".$details['photo__fileurl']."' ", 'person_id'=>$personId ));
					#Update some profile settings for the user in case they do not log out
					if($isUpdated) $this->native_session->set('__photo', $details['photo__fileurl']);
					# Remove the old image
					$photo = UPLOAD_DIRECTORY.'images/'.$user['photo'];
					if(!empty($user['photo'])) if(file_exists($photo)) unlink($photo);
				}
				else
				{
					$msg = "WARNING: The uploaded file format is not supported.";
				}
			}
			
			
			# Save the address if it is added
			if(!empty($details['contactaddress__addresstype']) || $this->native_session->get('contactaddress__addressline'))
			{
				$addressType = !empty($details['contactaddress__addresstype'])? $details['contactaddress__addresstype']: $this->native_session->get('contactaddress__addresstype');
				$addressLine = !empty($details['contactaddress__addressline'])? $details['contactaddress__addressline']: $this->native_session->get('contactaddress__addressline');
				$county = !empty($details['contactaddress__county'])? $details['contactaddress__county']: $this->native_session->get('contactaddress__county');
				$district = !empty($details['contactaddress__district'])? $details['contactaddress__district']: $this->native_session->get('contactaddress__district');
				$country = !empty($details['contactaddress__country'])? $details['contactaddress__country']: $this->native_session->get('contactaddress__country');
				
				$isUpdated = $this->_query_reader->run('update_address_data', array('parent_id'=>$personId, 'parent_type'=>'person', 'address_type'=>$addressType, 'importance'=>'contact', 'details'=>$addressLine, 'county'=>$county, 'district'=>$district, 'country'=>$country ));
			}
			
			# Notify to login again if the user's changes were successful
			$msg = $isUpdated? $msg.(empty($details['userid'])? " Please log out and login again to start using your new changes.": ""): $msg;
		}
		
		
		
		
		return array('boolean'=>$isUpdated, 'msg'=>$msg);
	}
	
	
	
	# Check if the password provided is valid
	function is_valid_password($password)
	{
		return (strlen($password) > 7 && preg_match('([a-zA-Z].*[0-9]|[0-9].*[a-zA-Z])', $password));
	}
	
	
			
	
	# Update the user password
	function update_password($userId, $newPassword)
	{
		$user = $this->_query_reader->get_row_as_array('get_user_by_id', array('user_id'=>$userId));
		$result1 = !empty($user)? $this->_query_reader->run('update_user_password', array('user_id'=>$userId, 'new_password'=>sha1($newPassword), 'old_password'=>$user['login_password'], 'updated_by'=>$this->native_session->get('__user_id') )): false;
		
		$result2 = $this->_messenger->send($userId, array('code'=>'password_recovery_notification', 'emailaddress'=>$user['login_name'], 'password'=>$newPassword, 'login_link'=>base_url() ));
		
		return get_decision(array($result1, $result2));
	}
	
	
	
	# Recover a user password
	function recover_password($details)
	{
		$result = false;
		$msg = '';
		
		if(is_valid_email($details['registeredemail']))
		{
			$user = $this->_query_reader->get_row_as_array('get_user_by_email', array('email_address'=>$details['registeredemail']));
			if(!empty($user))
			{
				$password = generate_temp_password();
				$result = $this->update_password($user['user_id'], $password);
				if($result) 
				{
					$result = $this->_messenger->send($user['user_id'], array('code'=>'password_recovery_notification', 'emailaddress'=>$details['registeredemail'], 'password'=>$password, 'login_link'=>base_url()), array('email'));
					if(!$result) $msg = "ERROR: The message with your temporary password could not be sent.";
				}
				else $msg = "ERROR: The password update failed.";
			}
			else $msg = "WARNING: There is no valid user with the given email address.";
		}
		else $msg = "WARNING: Please enter a valid email address.";
		
		return array('boolean'=>$result, 'msg'=>$msg);
	}
	

	
	# Change the role of the user
	function change_role($userId, $newRole)
	{
		# 1. Update the user's permission group
		$result1 = $this->_query_reader->run('update_user_permission_group', array('user_id'=>$userId, 'permission_group'=>$newRole, 'updated_by'=>$this->native_session->get('__user_id')));
		
		# 2. Notify the user about their new permission group
		if($result1) $result2 = $this->_messenger->send($userId, array('code'=>'notify_permission_change', 'email_from'=>NOREPLY_EMAIL, 'from_name'=>SITE_GENERAL_NAME, 'new_permission_group'=>$newRole, 'updated_by'=>$this->native_session->get('__full_name'), 'action_date'=>date('d-M-Y h:ia T', strtotime('now')), 'login_link'=>base_url() ));
		else $result2 = false;
		
		# 3. Log the event
		$result3 = $this->_logger->add_event(array('log_code'=>'change_user_role', 'result'=>($result1? 'success':'failed'), 'details'=>"user_id=".$userId."|new_role=".$newRole ));
		
		return get_decision(array($result1, $result2));
	}
	
	

	# Populate a user session profile
	function populate_session($userId, $isUpdatingSelf)
	{
		$profile = $this->_query_reader->get_row_as_array('get_user_profile', array('user_id'=>$userId));
		if(!empty($profile) && $isUpdatingSelf)
		{
			$this->native_session->set('profile_id', $profile['user_id']);
			$this->native_session->set('profile_personid', $profile['person_id']);
			$this->native_session->set('profile_loginname', $profile['login_name']);
			$this->native_session->set('profile_userrole', $profile['user_role']);
			$this->native_session->set('profile_lastname', $profile['last_name']);
			$this->native_session->set('profile_firstname', $profile['first_name']);
			$this->native_session->set('profile_signature', $profile['signature']);
			if(!empty($profile['telephone'])) $this->native_session->set('profile_telephone', $profile['telephone']);
			$this->native_session->set('profile_emailaddress', $profile['email_address']);
			$this->native_session->set('profile_photo', $profile['photo']);
			
			# Load the user's contact address
			$address = $this->_query_reader->get_row_as_array('get_user_address', array('user_id'=>$userId, 'address_type'=>'contact'));
			if(!empty($address))
			{
				$this->native_session->set('contactaddress__addresstype', $address['address_type']);
				$this->native_session->set('contactaddress__addressline', $address['addressline']);
				$this->native_session->set('contactaddress__county', $address['county']);
				$this->native_session->set('contactaddress__district', $address['district']);
				$this->native_session->set('contactaddress__district__hidden', $address['district_id']);
				$this->native_session->set('contactaddress__country', $address['country']);
				$this->native_session->set('contactaddress__country__hidden', $address['country_id']);
			}
		}
		#Setting the session for another user
		else if(!empty($profile))
		{
			$this->native_session->set('role__roles', $profile['user_role']);
			$this->native_session->set('lastname', $profile['last_name']);
			$this->native_session->set('firstname', $profile['first_name']);
			if(!empty($profile['telephone'])) $this->native_session->set('telephone', $profile['telephone']);
			$this->native_session->set('emailaddress', $profile['email_address']);
		}
		
	}
	


	# Clear a user session profile
	function clear_session()
	{
		$fields = array('role_roles'=>'', 'lastname'=>'', 'firstname'=>'', 'telephone'=>'', 'emailaddress'=>'');
		$this->native_session->delete_all($fields);
	}
	
	
	
		
	
	# Get list of users
	function get_list($instructions=array())
	{
		$searchString = " 1=1 ";
		# If a search phrase is sent in the instructions
		if(!empty($instructions['searchstring']))
		{
			$searchString .= " AND ".$instructions['searchstring'];
		}
		
		$orderBy = ($instructions['action'] == 'download')? " ORDER BY P.last_name ASC ": " ORDER BY U.last_updated DESC ";
		
		$count = !empty($instructions['pagecount'])? $instructions['pagecount']: NUM_OF_ROWS_PER_PAGE;
		$start = !empty($instructions['page'])? ($instructions['page']-1)*$count: 0;
		
		return $this->_query_reader->get_list('get_user_list_data', array('search_query'=>$searchString, 'limit_text'=>$start.','.($count+1), 'order_by'=>$orderBy));
	}
	
}


?>