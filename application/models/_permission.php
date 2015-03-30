<?php
/**
 * This class handles permissioning in the system.
 *
 * @author Al Zziwa <azziwa@gmail.com>
 * @version 1.1.0
 * @copyright TMIS
 * @created 01/08/2015
 */
class _permission extends CI_Model
{
	
	# Get permission list of a given user id
	function get_user_permission_list($userId)
	{
		# What's the user's permission group?
		if($this->native_session->get('permission_group'))
		{
			$userPermissionGroup = $this->native_session->get('permission_group');
		}
		else
		{
			$user = $this->_query_reader->get_row_as_array('get_user_by_id', array('user_id'=>$userId));
			$userPermissionGroup = $user['permission_group_id'];
		}
		return $this->get_group_permission_list($userPermissionGroup);
	}
	
	
	
	# Get group permission list
	function get_group_permission_list($groupId)
	{
		$permissions = array();
		$group = $this->_query_reader->get_row_as_array('get_group_by_id', array('group_id'=>$groupId));
		
		# Only proceed if the group exists
		if(!empty($group))
		{	
			$this->native_session->set('permission_group_name', $group['name']);
			$permissions = $this->_query_reader->get_single_column_as_array('get_group_permissions', 'code', array('group_id'=>$group['id']));
		}
		
		return $permissions;
	}
		
		
	
	# Add a new permission group
	function add_new_group($groupDetails)
	{
		# 1. Check if the required fields are entered
		$required = array('groupname', 'forsystem');
		$passed = process_fields($this, $groupDetails, $required);
		$msg = !empty($passed['msg'])? $passed['msg']: "";
		
		# 2. Save the data into the database
		if($passed['boolean'])
		{
			#Check if there is no such group
			$group_code = str_replace(' ', '_', strtolower($passed['data']['groupname']));
			$group = $this->_query_reader->get_row_as_array('get_permission_group_list', array('search_query'=>" name='".$group_code."' OR notes='".$passed['data']['groupname']."' ", 'limit_text'=>'1', 'order_by'=>''));
			
			if(empty($group))
			{
				$details = $passed['data'];
				#Do we have permissions submitted with the group?
				$defaultPermission = !empty($groupDetails['default'])? $groupDetails['default']: (!empty($groupDetails['permission'])? current($groupDetails['permission']): '');
			
				# 3. Save the group
				$groupId = $this->_query_reader->add_data('add_permission_group', array('group_name'=>$details['groupname'], 'is_system_only'=>strtoupper(substr($details['forsystem'], 0,1)), 'name_code'=>$group_code, 'default_permission'=>$defaultPermission, 'added_by'=>$this->native_session->get('__user_id') )); 
			
				if(empty($groupId)) $msg = "ERROR: The group details could not be saved.";
			
				# 4. Save the permissions if passed
				if(!empty($groupId) && !empty($groupDetails['permission'])) 
				{
					$result = $this->_query_reader->run('add_group_permissions', array('group_id'=>$groupId, 'permission_ids'=>"'".implode("','", $groupDetails['permission'])."'", 'added_by'=>$this->native_session->get('__user_id')));
				}
			}
			else
			{
				$msg = "WARNING: A group with that name or code already exists.";
			}
		}
		# Add the permissions to session 
		$this->native_session->set('permission',$groupDetails['permission']);

		return array('boolean'=>((!empty($groupId) && empty($groupDetails['permission'])) || (!empty($result) && $result)? true: false), 'msg'=>$msg); 
	}
		
		
	
	# Update group permissions.
	function update_group_permissions($groupId, $groupDetails)
	{
		$msg = "";
		$result = false;
		
		if(!empty($groupDetails['permission']))
		{
			# 1. Remove all permissions
			$result1 = $this->_query_reader->run('remove_group_permissions', array('group_id'=>$groupId));
		
			# 2. Add new permissions for the group - and update the last-updated-by field for the permission group
			$result2 = $this->_query_reader->run('add_group_permissions', array('group_id'=>$groupId, 'permission_ids'=>"'".implode("','", $groupDetails['permission'])."'", 'added_by'=>$this->native_session->get('__user_id')));
			
			$defaultPermission = !empty($groupDetails['default'])? $groupDetails['default']: (!empty($groupDetails['permission'])? current($groupDetails['permission']): '');
			$result3 = $this->_query_reader->run('update_permission_group', array('default_permission'=>$defaultPermission, 'group_id'=>$groupId, 'updated_by'=>$this->native_session->get('__user_id')));
			
			$result = get_decision(array($result1, $result2, $result3), FALSE);
		}
		else
		{
			$msg = "WARNING: There are no permissions selected.";
		}
		
		return array('boolean'=>$result, 'msg'=>$msg);
	}
		
	
	
	# Get list of permission
	function get_list($instructions=array())
	{
		$searchString = " 1=1 ";
		# If a search phrase is sent in the instructions
		if(!empty($instructions['searchstring']))
		{
			$searchString .= " AND ".$instructions['searchstring'];
		}
		
		$count = !empty($instructions['pagecount'])? $instructions['pagecount']: NUM_OF_ROWS_PER_PAGE;
		$start = !empty($instructions['page'])? ($instructions['page']-1)*$count: 0;
		
		if(!empty($instructions['action']) && in_array($instructions['action'], array('grouplist', 'updategroups')))
		{
			return $this->_query_reader->get_list('get_permission_group_list', array('search_query'=>$searchString, 'limit_text'=>$start.','.($count+1), 'order_by'=>" ORDER BY last_updated DESC, notes ASC "));
		}
		else if(!empty($instructions['action']) && $instructions['action'] == 'userlist')
		{
			return $this->_query_reader->get_list('get_user_permission_list', array('search_query'=>$searchString, 'limit_text'=>$start.','.($count+1), 'order_by'=>" ORDER BY P.last_name ASC, P.first_name ASC, G.notes ASC "));
		}
		else if(!empty($instructions['action']) && $instructions['action'] == 'grouppermissionlist')
		{
			return $this->_query_reader->get_list('get_group_permission_list', array('group_id'=>$instructions['group_id']));
		}
		else
		{
			return $this->_query_reader->get_list('get_permission_list', array('search_query'=>$searchString, 'limit_text'=>$start.','.($count+1), 'order_by'=>" ORDER BY category ASC "));
		}
	}
	
	
	

	# Populate a permission group session profile
	function populate_session($groupId)
	{
		$group = $this->_query_reader->get_row_as_array('get_permission_group_list', array('search_query'=>" G.id='".$groupId."' ", 'limit_text'=>'1', 'order_by'=>''));
		if(!empty($group))
		{
			$this->native_session->set('groupname', $group['notes']);
			$this->native_session->set('forsystem', ($group['is_system_only'] == 'Y'? 'YES': 'NO'));
			$this->native_session->set('default', $group['default_permission']);
			#Get the group permissions list
			$permissions = $this->_query_reader->get_single_column_as_array('get_group_permission_list', 'permission_id', array('group_id'=>$groupId));
			
			$this->native_session->set('permission', $permissions);
		}
	}
	
	
	
	# Verify a permission
	function verify($instructions)
	{
		$result = array('boolean'=>false, 'msg'=>'ERROR: The permission instructions could not be resolved.');
		
		if(!empty($instructions['action']))
		{
			switch($instructions['action'])
			{
				case 'reject':
					$result['boolean'] = $this->reject($instructions['id'],(!empty($instructions['reason'])? htmlentities($instructions['reason'], ENT_QUOTES): 'NONE'));
				break;
				
			}
			
			if(!empty($result['boolean'])) 
			{
				$result['msg'] = $result['boolean']? "The permission has been changed": "ERROR: The permission could not be changed.";
			}
		}
		
		return $result;
	}
	
	
	
	
	# Reject a permission addition
	function reject($groupId, $reason)
	{
		$group = $this->_query_reader->get_row_as_array('get_permission_group_list', array('search_query'=>" G.id='".$groupId."' ", 'limit_text'=>'1', 'order_by'=>''));
		
		$result1 = $this->_messenger->send($group['added_by'], array('code'=>'reject_permission_group', 'reason'=>$reason), array('email'));
		$result2 = $this->_query_reader->run('delete_permission_group_data', array('group_id'=>$groupId));
		
		return get_decision(array($result1,$result2), FALSE);
	}
}


?>