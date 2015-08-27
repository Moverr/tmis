<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This class controls viewing public pages.
 *
 * @author Al Zziwa <azziwa@gmail.com>
 * @version 1.1.0
 * @copyright TMIS
 * @created 01/08/2015
 */

class Page extends CI_Controller
{
	#Constructor to set some default values at class load
	public function __construct()
    {
        parent::__construct();
		$this->load->helper('form');
	}


	#The home page
	function index()
	{
		$data = filter_forwarded_data($this);
		$this->load->model('_vacancy');

		$data['list'] = $this->_vacancy->get_list(array('action'=>'public'));
		$this->load->view('home', $data);
	}


	#Function to show the full image details
	function view_image_details()
	{
		$data = filter_forwarded_data($this);

		if(!empty($data['u']))
		{
			$data['url'] = base_url()."assets/uploads/images/".decrypt_value($data['u']);
		}

		$data['area'] = "show_bigger_image";
		$this->load->view('addons/basic_addons', $data);
	}


	#Function to show the system about us page
	function about_us()
	{
		$data = filter_forwarded_data($this);

		$this->load->view('page/about_us', $data);
	}

	#Function to show the system terms of reference
	function terms_of_use()
	{
		$data = filter_forwarded_data($this);

		$this->load->view('page/terms_of_use', $data);
	}



	#Function to show the system privacy policy
	function privacy_policy()
	{
		$data = filter_forwarded_data($this);

		$this->load->view('page/privacy_policy', $data);
	}



	#Function to show the system FAQs
	function faqs()
	{
		$data = filter_forwarded_data($this);

		$this->load->view('page/faqs', $data);
	}




	#Load a slow page and shwo a temporary message in the process
	function load_slow_page()
	{
		$data = filter_forwarded_data($this);

		if(!empty($data['p']) && !empty($data['t']))
		{
			$data['pageUrl'] = decrypt_value($data['p']);
			$data['pageTitle'] = SITE_TITLE.": ".decrypt_value($data['t']);
			$data['loadingMessage'] = !empty($data['m'])?decrypt_value($data['m']): 'Loading..';
		}
		else
		{
			$this->native_session->set('amsg', "WARNING: You do not have sufficient priviliges to access the desired page. <br>Please contact your administrator.");
			$data['pageUrl'] = base_url().'account/logout/m/amsg';
			$data['pageTitle'] = SITE_TITLE.": Logout";
			$data['loadingMessage'] = 'Loading..';
		}

		$this->load->view('page/loading', $data);
	}





	#Notify the user that their session is about to expire
	function refresh_session()
	{
		$data = filter_forwarded_data($this);
		#Refresh the user session
		if(!empty($data['u']))
		{
			$this->native_session->set('userId', decrypt_value($data['u']));
			$data['area'] = "blank_area_msg";
			$this->load->view('addons/basic_addons', $data);
		}
		else
		{
			$data['area'] = "notify_session_refresh";
			$this->load->view('addons/basic_addons', $data);
		}
	}



	#Function to handle contact us page submissions
	function contact_us()
	{
		$data = filter_forwarded_data($this);

		# User has posted a message
		if(!empty($_POST))
		{
			$passed = process_fields($this, $this->input->post(NULL, TRUE), array('yourname','emailaddress', 'reason__contactreason', 'details'), array('@','!'));
			$data['msg'] = !empty($passed['msg'])? $passed['msg']: "";

			# All required fields are included? Then send the message to the admin
			if($passed['boolean'])
			{
				$details = $passed['data'];

				$data['result'] = $this->_messenger->send_email_message('', array('code'=>'contact_us_message', 'emailfrom'=>NOREPLY_EMAIL, 'telephone'=>(!empty($details['telephone'])? $details['telephone']:''), 'fromname'=>SITE_GENERAL_NAME, 'cc'=>$details['emailaddress'], 'useremailaddress'=>$details['emailaddress'], 'usernames'=>$details['yourname'], 'subject'=>$details['reason__contactreason'], 'details'=>$details['details'], 'emailaddress'=>HELP_EMAIL, 'login_link'=>base_url(), 'sent_time'=>date('d-M-Y h:ia T', strtotime('now')) ));

				if($data['result'])
				{
					$this->native_session->delete_all(array('yourname'=>'','emailaddress'=>'', 'reason__contactreason'=>'', 'details'=>''));
					$data['msg'] = "Your message has been sent. We shall respond as soon as possible.";
				}
				else $data['msg'] = "ERROR: There was a problem sending your message";
			}
			else
			{
				$data['msg'] = "WARNING: There is a problem with the data you submitted.";
			}
		}

		$this->load->view('page/contact_us', $data);
	}


	# Show the address field form
	function address_field_form()
	{
		$data = filter_forwarded_data($this);

		$data['area'] = "address_field_form";
		$this->load->view('addons/basic_addons', $data);
	}


	# Copy address data from one field to another
	function copy_address_data()
	{
		$data = filter_forwarded_data($this);
		# copy over the address data
		$result = !empty($data['from']) && !empty($data['to'])? copy_address($this, $data):false;

		$data['area'] = "address_field_form";
		$this->load->view('addons/basic_addons', $data);
	}


	# Remove address data from a field
	function remove_address_data()
	{
		$data = filter_forwarded_data($this);
		# remove address data
		$result = !empty($data['field_id'])? remove_address($this, $data):false;

		$data['area'] = "address_field_form";
		$this->load->view('addons/basic_addons', $data);
	}


	# Get a customized drop down list
	function get_custom_drop_list()
	{
		$data = filter_forwarded_data($this);
	 

		if(!empty($data['type'])){
			$searchBy = !empty($data['search_by'])? $data['search_by']: '';
		#	print_r($data); exit();
			$data['list'] =  get_option_list($this, $data['type'], 'div', $searchBy, $data);
		}

		$data['area'] = "dropdown_list";
		$this->load->view('addons/basic_addons', $data);
	}


	# Get values filled in by a form layer and put them in a session for layer use
	function get_layer_form_values()
	{
		$data = filter_forwarded_data($this);

		switch($data['type'])
		{
			case 'address':
				$data = !empty($_POST)? array_merge($data, $this->input->post(NULL, TRUE)): $data;
				# Verify and clean up the fields and put them in the session for use layer
				process_fields($this, $data);
				$data['msg'] = "data added";
			break;

			case 'verify_document':
				$this->load->model('_validator');
				$result = $this->_validator->is_valid_document($_POST);
				$data['msg'] = !empty($result)? 'The document is valid.<br><br>It was issued to '.$result['owner_name'].' on '.date('d-M-Y', strtotime($result['date_added'])) : 'WARNING: Document is invalid.';
			break;

			default:
			break;
		}

		$data['area'] = "basic_msg";
		$this->load->view('addons/basic_addons', $data);
	}



	# Download a document
	function download()
	{
		$data = filter_forwarded_data($this);
		if(!empty($data['folder']) && !empty($data['file'])) force_download($data['folder'],$data['file']);
	}


	# Verify a document
	function verify()
	{
		$data = filter_forwarded_data($this);

		$this->load->view('page/verify_document', $data);
	}


















	# Test send an SMS
	function test()
	{
		/*$this->load->library('Sms_global', array('user'=>SMS_GLOBAL_USERNAME, 'pass'=>SMS_GLOBAL_PASSWORD, 'from'=>SMS_GLOBAL_VERIFIED_SENDER));

		$this->sms_global->to('256784000808');
		$this->sms_global->from('16786442425');
		$this->sms_global->message('THIS IS A TMIS TEST SMS FROM AL. DROP ME AN EMAIL IF YOU RECEIVE THIS.');
		$this->sms_global->send();

		# only use this to output the message details on screen for debugging
		#$this->sms_global->print_debugger();

		$isSent = !empty($this->sms_global->get_sms_id())? true: false;


		#$isSent = $this->_messenger->send_email_message('', array('emailfrom'=>'admin@tmis.go.ug', 'fromname'=>'TMIS TEST', 'emailaddress'=>'azziwa@gmail.com', 'subject'=>'A test email', 'details'=>'This is testing the attachement.<br>Al', 'fileurl'=>UPLOAD_DIRECTORY.'documents/file_1423958732.pdf'));

		*/

		$this->load->model('_approval_chain');
		$chain = $this->_query_reader->get_row_as_array('get_approval_chain_by_id', array('chain_id'=>'106'));

		$user = $this->_query_reader->get_row_as_array('get_user_profile', array('user_id'=>$chain['subject_id']));

		$actionDetails['date_today'] = date('d-M-Y', strtotime('now'));
		$actionDetails['asset_folder'] = BASE_URL."assets/";
		$actionDetails['document_size'] = 'A4';
		$actionDetails['document_orientation'] = 'landscape';
		$actionDetails['teacher_name'] = strtoupper($user['last_name'].', '.$user['first_name']);
		$actionDetails['teacher_grade'] = 'PRIMARY EDUCATION - GRADE 4';
		$actionDetails['effective_date'] = '09-Feb-2015';
		$actionDetails['tracking_number'] = $actionDetails['certificate_number'] = '20041423958732';
		$approver = $this->_query_reader->get_row_as_array('get_user_profile', array('user_id'=>'25'));
		$actionDetails['signature_url'] = $approver['signature'];

		$isSent = $this->_approval_chain->send_document($chain, 'registration_certificate', array('system'), $actionDetails);

		if($isSent) echo "WOW! MESSAGE SENT";
		else echo "SORRY! NOT SENT";
	}



}

/* End of controller file */
