<?php
/**
 * This class searches for data on the system.
 *
 * @author Al Zziwa <azziwa@gmail.com>
 * @version 1.1.0
 * @copyright TMIS
 * @created 01/08/2015
 */
class _finder extends CI_Model
{
	
	# Load a list of search items
	function load_list($instructions)
	{
		$data = $instructions;
		$list = array();
		# Get the search parameters passed with the instructions
		$data['page'] = !empty($data['p']) && empty($data['__clear'])? $data['p']: 1;
		$data['pagecount'] = !empty($data['n']) && empty($data['__clear'])? $data['n']: NUM_OF_ROWS_PER_PAGE;
		$data['phrase'] = !empty($data['phrase']) && empty($data['__clear'])? addslashes(restore_bad_chars($data['phrase'])): '';
		$data['searchby'] = !empty($data['searchby']) && empty($data['__clear'])? explode('--', $data['searchby']): '';
		
		if(!empty($instructions['type']))
		{
			$data['listid'] = $instructions['type'].'search';
			switch($instructions['type'])
			{
				case 'user':
					$this->load->model('_user');
					#Did the UI send any fields to search by?
					if(empty($data['__clear']))
					{
						$searchBy = !empty($data['searchby'])? $data['searchby']: array('P.first_name', 'P.last_name');
						$data['searchstring'] = $this->generate_phrase_query($searchBy, $data['phrase']);
					}
					$list = $this->_user->get_list($data);
				break;
				
				
				case 'vacancy':
					$this->load->model('_vacancy');
					#Did the UI send any fields to search by?
					if(empty($data['__clear']))
					{
						$searchBy = !empty($data['searchby'])? $data['searchby']: array('V.topic', 'V.summary', 'I.name');
						$data['searchstring'] = $this->generate_phrase_query($searchBy, $data['phrase']);
					}
					$list = $this->_vacancy->get_list($data);
				break;
				
				
				case 'permission':
					$this->load->model('_permission');
					#Did the UI send any fields to search by?
					if(empty($data['__clear']))
					{
						# Default search fields
						if(!empty($data['action']) && $data['action'] == 'grouplist') $default = array('notes');
						else if(!empty($data['action']) && $data['action'] == 'userlist') $default = array('P.first_name', 'P.last_name');
						else $default = array('display');
						
						$searchBy = !empty($data['searchby'])? $data['searchby']: $default;
						$data['searchstring'] = $this->generate_phrase_query($searchBy, $data['phrase']);
					}
					$list = $this->_permission->get_list($data);
				break;
				
				
				case 'message':
					$this->load->model('_messenger');
					#Did the UI send any fields to search by?
					if(empty($data['__clear']))
					{
						$searchBy = !empty($data['searchby'])? $data['searchby']: array('P.first_name', 'P.last_name', 'M.subject');
						$data['searchstring'] = $this->generate_phrase_query($searchBy, $data['phrase']);
					}
					$list = $this->_messenger->get_list($data);
				break;
				
				
				case 'school':
					$this->load->model('_school');
					#Did the UI send any fields to search by?
					if(empty($data['__clear']))
					{
						$searchBy = !empty($data['searchby'])? $data['searchby']: array('S.name');
						$data['searchstring'] = $this->generate_phrase_query($searchBy, $data['phrase']);
					}
					$list = $this->_school->get_list($data);
				break;
				
				
				case 'census':
					$this->load->model('_census');
					#Did the UI send any fields to search by?
					if(empty($data['__clear']))
					{
						$searchBy = !empty($data['searchby'])? $data['searchby']: array('P.first_name', 'P.last_name');
						$data['searchstring'] = $this->generate_phrase_query($searchBy, $data['phrase']);
					}
					$list = $this->_census->get_list($data);
				break;
				
				
				case 'teacher':
					$this->load->model('_teacher');
					#Did the UI send any fields to search by?
					if(empty($data['__clear']))
					{
						$searchBy = !empty($data['searchby'])? $data['searchby']: array('P.first_name', 'P.last_name');
						$data['searchstring'] = $this->generate_phrase_query($searchBy, $data['phrase']);
					}
					$list = $this->_teacher->get_list($data);
				break;
				
				
				case 'job':
					$this->load->model('_job');
					#Did the UI send any fields to search by?
					if(empty($data['__clear']))
					{
						$searchBy = !empty($data['searchby'])? $data['searchby']: array('V.topic', 'V.summary', 'I.name');
						$data['searchstring'] = $this->generate_phrase_query($searchBy, $data['phrase']);
					}
					$list = $this->_job->get_list($data);
				break;
				
				
				case 'interview':
					$this->load->model('_interview');
					#Did the UI send any fields to search by?
					if(empty($data['__clear']))
					{
						if(in_array($instructions['action'], array('setdate', 'recommend', 'recommendations'))) 
							$default = array('V.topic', 'V.summary', 'I.name');
						else if($instructions['action'] == 'shortlist') $default = array('S.shortlist_name', 'V.topic', 'I.name');
						else $default = array('P.first_name', 'P.last_name', 'V.topic');
						
						$searchBy = !empty($data['searchby'])? $data['searchby']: $default;
						$data['searchstring'] = $this->generate_phrase_query($searchBy, $data['phrase']);
					}
					$list = $this->_interview->get_list($data);
				break;
				
				
				case 'confirmation':
					$this->load->model('_confirmation');
					#Did the UI send any fields to search by?
					if(empty($data['__clear']))
					{
						$searchBy = !empty($data['searchby'])? $data['searchby']: array('P1.last_name', 'P1.first_name', 'D.name', 'I.name');
						$data['searchstring'] = $this->generate_phrase_query($searchBy, $data['phrase']);
					}
					$list = $this->_confirmation->get_list($data);
				break;
				
				
				case 'retirement':
					$this->load->model('_retirement');
					#Did the UI send any fields to search by?
					if(empty($data['__clear']))
					{
						$searchBy = !empty($data['searchby'])? $data['searchby']: array('P.last_name', 'P.first_name');
						$data['searchstring'] = $this->generate_phrase_query($searchBy, $data['phrase']);
					}
					$list = $this->_retirement->get_list($data);
				break;
				
				
				case 'leave':
					$this->load->model('_leave');
					#Did the UI send any fields to search by?
					if(empty($data['__clear']))
					{
						$searchBy = !empty($data['searchby'])? $data['searchby']: array('P.last_name', 'P.first_name');
						$data['searchstring'] = $this->generate_phrase_query($searchBy, $data['phrase']);
					}
					$list = $this->_leave->get_list($data);
				break;
				
				
				case 'transfer':
					$this->load->model('_transfer');
					#Did the UI send any fields to search by?
					if(empty($data['__clear']))
					{
						$searchBy = !empty($data['searchby'])? $data['searchby']: array('P.last_name', 'P.first_name', 'I.name');
						$data['searchstring'] = $this->generate_phrase_query($searchBy, $data['phrase']);
					}
					$list = $this->_transfer->get_list($data);
				break;
				
				
				case 'report':
					$this->load->model('_report');
					#Did the UI send any fields to search by?
					if(empty($data['__clear']))
					{
						$searchBy = !empty($data['searchby'])? $data['searchby']: array('P.last_name', 'P.first_name', 'L.details');
						$data['searchstring'] = $this->generate_phrase_query($searchBy, $data['phrase']);
						if(strpos($data['phrase'], '@') !== false) $data['searchstring'] = " (L.details LIKE '%email=".$data['phrase']."%|%' OR L.details LIKE '%username=".$data['phrase']."%|%') ";
					}
					$list = $this->_report->get_list($data);
				break;
				
				
				case 'approval':
					$this->load->model('_approval_chain');
					#Did the UI send any fields to search by?
					if(empty($data['__clear']))
					{
						$searchBy = !empty($data['searchby'])? $data['searchby']: array('P.last_name', 'P.first_name', 'A.chain_type');
						$data['searchstring'] = $this->generate_phrase_query($searchBy, $data['phrase']);
					}
					$list = $this->_approval_chain->get_list($data);
				break;
				
				
				
			}
		}
		$data['list'] = $list;
		return $data;
	}
		




	# Format the search query to be used in the search
	function generate_phrase_query($searchBy, $phrase)
	{
		$query = array();
		$phrase = htmlentities($phrase, ENT_QUOTES);
		
		foreach($searchBy AS $field)
		{
			array_push($query, "(".$field." LIKE '".$phrase."%' OR ".$field." LIKE '% ".$phrase."%')");
		}
		
		return " (".implode(" OR ", $query).") ";
	}

}


?>