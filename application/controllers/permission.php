<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This class controls viewing permission pages on the system.
 *
 * @author Al Zziwa <azziwa@gmail.com>
 * @version 1.1.0
 * @copyright TMIS
 * @created 01/20/2015
 */

class Permission extends CI_Controller 
{
	#Constructor to set some default values at class load
	public function __construct()
    {
        parent::__construct();
		$this->load->model('_permission');
	}
	
	
	
	
	# View list of permissions
	function lists()
	{
		$data = filter_forwarded_data($this);
		check_access($this, 'view_permission_list');
		
		$data['list'] = $this->_permission->get_list();
		$this->load->view('permission/list_permissions', $data); 
	}
	
	
	# View list of permission groups
	function group_list()
	{
		$data = filter_forwarded_data($this);
		check_access($this, 'view_permission_group_list');
		
		$data['list'] = $this->_permission->get_list(array('action'=>'grouplist'));
		$data['action'] = 'grouplist';
		$this->load->view('permission/list_permissions', $data); 
	}
	
	
	
	# View list of users and their permissions
	function user_list()
	{
		$data = filter_forwarded_data($this);
		check_access($this, 'view_user_permissions');
		
		$data['list'] = $this->_permission->get_list(array('action'=>'userlist'));
		$data['action'] = 'userlist';
		$this->load->view('permission/list_permissions', $data); 
	}
	
	
	# View group permissions
	function group_permissions()
	{
		$data = filter_forwarded_data($this);
		
		$data['list'] = $this->_permission->get_list(array('action'=>'grouppermissionlist', 'group_id'=>$data['id']));
		$data['area'] = 'permission_list';
		$this->load->view('addons/basic_addons', $data); 
	}
	
	
	# Add a permission group
	function add_group()
	{
		$data = filter_forwarded_data($this);
		check_access($this, 'add_new_permission_group');
		$fields = array('groupname'=>'', 'permissions'=>'', 'permission'=>'', 'default'=>'', 'forsystem'=>'', 'telephone'=>'');
		$this->native_session->delete_all($fields);
		
		#User has posted the form
		if(!empty($_POST))
		{
			$data['result'] = $this->input->post('groupid')? $this->_permission->update_group_permissions($this->input->post('groupid'), $this->input->post(NULL, TRUE)): $this->_permission->add_new_group($this->input->post(NULL, TRUE));
			
			$data['msg'] = $data['result']['boolean'] && empty($data['result']['msg'])? "The group details have been saved.": $data['result']['msg'];
			
			# Redirect to appropriate page if successful
			if($data['result']['boolean'])
			{
				$this->native_session->delete_all($fields);
				$this->native_session->set('msg', $data['msg']);
				if($this->input->post('groupid')) $data['forward'] = base_url().$this->input->post('forward');
				if(!$this->input->post('groupid') && $this->input->post('forward')) redirect(base_url().$this->input->post('forward'));
			}
			
			
		}
		
		#Put the data in the session that is going to be edited
		if(!empty($data['id']) && empty($_POST)) $this->_permission->populate_session($data['id']);
		
		# High page count is to help return all of the permissions 
		$data['permission_list'] = $this->_permission->get_list(array('pagecount'=>1000));
		$this->load->view('permission/new_group', $data); 
	}
	
	
	#Update a group's permissions
	function update_group()
	{
		$data = filter_forwarded_data($this);
		check_access($this, 'change_group_permissions');
		
		$data['list'] = $this->_permission->get_list(array('action'=>'updategroups'));
		$data['action'] = 'updategroups';
		$this->load->view('permission/list_permissions', $data); 
	}
	
	
	
	# Verify the permission group
	function verify()
	{
		$data = filter_forwarded_data($this);
		if(!empty($_POST))
		{
			# Approve or reject a permission group
			$result = $this->_permission->verify($_POST);
			$this->native_session->set('msg', ($result['boolean']? "The permission group has been rejected.": (!empty($result['msg'])? $result['msg']: "ERROR: The user could not be rejected.") ));
		}
		else
		{
			# Get list type
			$data['list_type'] = current(explode("_", $data['action']));
			$data['area'] = 'verify_permission';
			$this->load->view('addons/basic_addons', $data);
		}
	}
}

/* End of controller file */