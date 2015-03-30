<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This class controls viewing messages on the system.
 *
 * @author Al Zziwa <azziwa@gmail.com>
 * @version 1.1.0
 * @copyright TMIS
 * @created 01/20/2015
 */

class Message extends CI_Controller 
{
	# Send new system message
	function send_new_system()
	{
		$data = filter_forwarded_data($this);
		check_access($this, 'send_system_message');
		if(empty($data['reply'])) $this->_messenger->clear_session();
		
		#Sending a system message
		if(!empty($_POST))
		{
			$data['result'] = $this->_messenger->send_from_form('system', $this->input->post(NULL, TRUE));
			# Remove the session variables whether the message was sent or not
			$this->_messenger->clear_session();
			$data['area'] = "message_sending_results";
			$this->load->view('addons/basic_addons', $data);
		}
		else
		{
			$data['action'] = "send_new_system";
			$this->load->view('message/new_message', $data); 
		}
	}
	
	
	
	# Send new email message
	function send_new_email()
	{
		$data = filter_forwarded_data($this);
		check_access($this, 'send_email_message');
		if(empty($data['reply'])) $this->_messenger->clear_session();
		
		#Sending a system message
		if(!empty($_POST))
		{
			$data['result'] = $this->_messenger->send_from_form('email', $this->input->post(NULL, TRUE));
			# Remove the session variables whether the message was sent or not
			$this->_messenger->clear_session();
			$data['area'] = "message_sending_results";
			$this->load->view('addons/basic_addons', $data);
		}
		else
		{
			$data['action'] = "send_new_email";
			$this->load->view('message/new_message', $data); 
		}
	}
	
	
	# Send new sms message
	function send_new_sms()
	{
		$data = filter_forwarded_data($this);
		check_access($this, 'send_sms_message');
		if(empty($data['reply'])) $this->_messenger->clear_session();
		
		#Sending an sms message
		if(!empty($_POST))
		{
			$data['result'] = $this->_messenger->send_from_form('sms', $this->input->post(NULL, TRUE));
			# Remove the session variables whether the message was sent or not
			$this->_messenger->clear_session();
			$data['area'] = "message_sending_results";
			$this->load->view('addons/basic_addons', $data);
		}
		else
		{
			$data['action'] = "send_new_sms";
			$this->load->view('message/new_message', $data); 
		}
	}
	
	
	
	# View message inbox
	function inbox()
	{
		$data = filter_forwarded_data($this);
		check_access($this, 'view_message_inbox');
		
		$data['action'] = 'inbox';
		$data['list'] = $this->_messenger->get_list(array('action'=>'inbox'));
		$this->load->view('message/list_messages', $data); 
	}
	
	
	# View message archive
	function archive()
	{
		$data = filter_forwarded_data($this);
		check_access($this, 'view_archived_messages');
		
		$data['action'] = 'archive';
		$data['list'] = $this->_messenger->get_list(array('action'=>'archive'));
		$this->load->view('message/list_messages', $data);  
	}
	
	
	# View sent messages
	function sent()
	{
		$data = filter_forwarded_data($this);
		check_access($this, 'view_sent_messages');
		
		$data['action'] = 'sent';
		$data['list'] = $this->_messenger->get_list(array('action'=>'sent'));
		$this->load->view('message/list_messages', $data); 
	}
	
		
	
	# Verify the message - just keeping the function name consistent
	function verify()
	{
		$data = filter_forwarded_data($this);
		if(!empty($_POST))
		{
			# Verify a message
			$result = $this->_messenger->verify($_POST);
			
			$actionPart = current(explode("_", $_POST['action']));
			$actions = array('archive'=>'archived', 'restore'=>'restored');
			$actionWord = !empty($actions[$actionPart])? $actions[$actionPart]: 'made';
			$this->native_session->set('msg', ($result['boolean']? "The message has been ".$actionWord: "ERROR: The message could not be ".$actionWord ));
		}
		else
		{
			# Get list type
			$data['list_type'] = current(explode("_", $data['action']));
			$data['area'] = 'verify_user';
			$this->load->view('addons/basic_addons', $data);
		}
	}
	
	
	
	# Reply to a message
	function reply()
	{
		$data = filter_forwarded_data($this);
		
		# 1. Prefill the message details with previous message
		if(!empty($data['id'])) $this->_messenger->populate_session($data['id']);
		else $this->native_session->set('msg', 'ERROR: We can not resolve the previous message.');
		
		# 2. Redirect to appropriate send format page to respond
		if(empty($data['format'])) $this->native_session->set('msg', 'ERROR: We can not resolve the send format.');
		redirect(!empty($data['format'])? base_url().'message/send_new_'.$data['format'].'/reply/message': base_url().'message/inbox');
	}
	
	
	
	# View details of a message
	function details()
	{
		$data = filter_forwarded_data($this);
		
		if(!empty($data['id'])) {
			$data['message'] = $this->_messenger->message_details($data['id']);
			$this->_messenger->change_status($data['id'], 'read');
		} else $data['msg'] = 'ERROR: We can not resolve the message.';
		
		$data['area'] = 'message_details';
		$this->load->view('addons/basic_addons', $data);
	}
	
	
}

/* End of controller file */