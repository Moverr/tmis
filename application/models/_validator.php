<?php
/**
 * This class handles validation of data in the system.
 *
 * @author Al Zziwa <azziwa@gmail.com>
 * @version 1.1.0
 * @copyright TMIS
 * @created 01/08/2015
 */
class _validator extends CI_Model
{
	
	# Check if this is a valid user account
	function is_valid_account($accountDetails)
	{
		$boolean = false;
		$userId = "";
		
		$user = $this->_query_reader->get_row_as_array('get_user_by_name_and_pass', array('login_name'=>$accountDetails['login_name'], 'login_password'=>sha1($accountDetails['login_password']) ));
		if(!empty($user))
		{
			$boolean = true;
			$userId = $user['id'];
			
			# Set the user's session variables
			
			# Start each variable with two underscores to uniquely mark these as this user's profile variables 
			# and should not modified by other system functions
			$this->native_session->set('__user_id', $user['id']);
			$this->native_session->set('__email_address', $user['email_address']);
			$this->native_session->set('__person_id', $user['person_id']);
			if(!empty($user['telephone'])) $this->native_session->set('__telephone', $user['telephone']);
			$this->native_session->set('__permission_group', $user['permission_group_id']);
			$this->native_session->set('__permission_group_name', $user['permission_group_name']);
			$this->native_session->set('__default_permission', $user['default_permission_code']);
			$this->native_session->set('__first_name', $user['first_name']);
			$this->native_session->set('__last_name', $user['last_name']);
			$this->native_session->set('__full_name', $user['last_name'].', '.$user['first_name']);
			$this->native_session->set('__gender', $user['gender']);
			$this->native_session->set('__date_of_birth', $user['date_of_birth']);
			$this->native_session->set('__signature', $user['signature']);
			$this->native_session->set('__teacher_status', $user['teacher_status']);
			$this->native_session->set('__posting', $user['user_posting']);
			if(!empty($user['photo'])) $this->native_session->set('__photo', $user['photo']);
		}
		
		return array('boolean'=>$boolean, 'user_id'=>$userId);
	}
	
	
	
	
	# Check if this is a valid confirmation code
	function is_valid_confirmation_code($personId, $code)
	{
		$isValid = false;
		
		if(strlen($code) > 2)
		{
			$hexCode = strrev(substr($code, 0, (strlen($code)-2))); 
			$isValid = (hexdec($hexCode) == $personId)? true: false;
		}
		
		return $isValid;
	}
	


	# Validate a document 
	function is_valid_document($details)
	{
		if(!empty($details['documenttype']) && !empty($details['trackingnumber']))
		{
			$document = $this->_query_reader->get_row_as_array('validate_system_document', array('document_type'=>$details['documenttype'], 'tracking_number'=>trim($details['trackingnumber']) )); 
		}
		
		return !empty($document)? $document: array();
	}

}


?>