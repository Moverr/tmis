<?php
/**
 * This class creates and manages census data.
 *
 * @author Al Zziwa <azziwa@gmail.com>
 * @version 1.1.0
 * @copyright TMIS
 * @created 01/30/2015
 */
class _census extends CI_Model
{


	# Add a new census
	function add_new($censusDetails)
	{
	#	print_r($censusDetails); exit();
		$isAdded = false;
		$required = array('teachername__teachers', 'teacherid', 'censusstart', 'censusend', 'averageworkload','mpsrollnumber','subjectspecialization__subjectspecialization__hidden',);
 
		# 1. Add all provided data into the session
		$passed = process_fields($this, $censusDetails, $required, array("-","/"));
		#print_r($passed); exit();
		$msg = !empty($passed['msg'])? $passed['msg']: "";
		#echo "<BR>MESSAGE: ".$msg;
		# 2. Save the data into the database
		if($passed['boolean'])
		{
			#echo "<BR>IN HERE";
			$details = $passed['data'];
			$censusId = $this->_query_reader->add_data('add_census_data', array('teacher_id'=>$details['teacherid'], 'start_date'=>format_date($details['censusstart'], 'YYYY-MM-DD', ''), 'end_date'=>format_date($details['censusend'], 'YYYY-MM-DD', ''), 'weekly_workload_average'=>$details['averageworkload'], 'added_by'=>$this->native_session->get('__user_id') ,'mpsrollnumber'=>$details['mpsrollnumber'],'subjectid' => $details['subjectspecialization__subjectspecialization__hidden'],'last_uploaded'));

			 $isAdded = !empty($censusId)? true: false;
			 if($isAdded)
			 {
			 	# Add responsibilities and training
				 if(!empty($censusDetails['responsibility'])) $this->_query_reader->run('add_census_responsibility', array('census_id'=>$censusId, 'responsibility_ids'=>"'".implode("','",$censusDetails['responsibility'] )."'"));

				 if(!empty($censusDetails['training'])) $this->_query_reader->run('add_census_training', array('census_id'=>$censusId, 'training_ids'=>"'".implode("','",$censusDetails['training'] )."'"));

				 $this->native_session->delete_all($details);
			 }
		}

		return array('boolean'=>$isAdded, 'msg'=>$msg, 'id'=>(!empty($censusId)? $censusId: ''));
	}



	# Update the census data
	function update($censusId, $censusDetails)
	{
		$isUpdated = false;
		$required = array('censusstart', 'censusend', 'averageworkload');
		# 1. Add all provided data into the session
		$passed = process_fields($this, $censusDetails, $required, array("-"));
		$msg = !empty($passed['msg'])? $passed['msg']: "";
		# 2. Save the data into the database
		if($passed['boolean'])
		{
			$details = $passed['data'];
			$isUpdated = $this->_query_reader->run('update_census_data', array('census_id'=>$censusId, 'start_date'=>format_date($details['censusstart'], 'YYYY-MM-DD', ''), 'end_date'=>format_date($details['censusend'], 'YYYY-MM-DD', ''), 'weekly_workload_average'=>$details['averageworkload'], 'updated_by'=>$this->native_session->get('__user_id') ));

			if($isUpdated)
			{
				# a) First clear all the old reponsibilities and training
				$result1 = $this->_query_reader->run('remove_census_responsibility', array('census_id'=>$censusId));
				$result2 = $this->_query_reader->run('remove_census_training', array('census_id'=>$censusId));

				# b) Add the new responsibilities and training
				if($result1 && !empty($details['responsibility'])) $this->_query_reader->run('add_census_responsibility', array('census_id'=>$censusId, 'responsibility_ids'=>"'".implode("','",$details['responsibility'] )."'"));

				if($result2 && !empty($details['training'])) $this->_query_reader->run('add_census_training', array('census_id'=>$censusId, 'training_ids'=>"'".implode("','",$details['training'] )."'"));

				$this->native_session->delete_all($details);
			}

			if($isUpdated) $this->native_session->delete_all($details);
		}

		return array('boolean'=>$isUpdated, 'msg'=>$msg, 'id'=>$censusId);
	}





	# Populate a census session profile
	function populate_session($censusId)
	{
		$census = $this->_query_reader->get_row_as_array('get_census_list', array('search_query'=>" C.id='".$censusId."' ", 'limit_text'=>'1', 'order_by'=>' P.last_name '));
		if(!empty($census))
		{
			$this->native_session->set('teachername__teachers', $census['teacher_name']);
			$this->native_session->set('censusstart', $census['start_date']);
			$this->native_session->set('censusend', $census['end_date']);
			$this->native_session->set('averageworkload', $census['weekly_workload_average']);

			#Get the responsibility list
			$responsibilities = $this->_query_reader->get_single_column_as_array('get_census_responsibility_list', 'responsibility_id', array('census_id'=>$censusId));
			$this->native_session->set('responsibility', $responsibilities);

			#Get the training list
			$training = $this->_query_reader->get_single_column_as_array('get_census_training_list', 'training_id', array('census_id'=>$censusId));
			$this->native_session->set('training', $training);
		}
	}



	# Clear a census session profile
	function clear_session()
	{
		$fields = array('teachername__teachers'=>'', 'responsibility'=>'', 'training'=>'', 'censusstart'=>'', 'censusend'=>'', 'averageworkload'=>'');
		$this->native_session->delete_all($fields);
	}





	# Get list of census
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

		if(!empty($instructions['action']) && $instructions['action'] == 'responsibility')
		{
			return $this->_query_reader->get_list('get_responsibility_list');
		}
		else if(!empty($instructions['action']) && $instructions['action'] == 'training')
		{
			return $this->_query_reader->get_list('get_training_list');
		}
		else if(!empty($instructions['action']) && $instructions['action'] == 'training_sub_list')
		{
			return $this->_query_reader->get_list('get_census_training', array('census_id'=>$instructions['census_id']));
		}
		else if(!empty($instructions['action']) && $instructions['action'] == 'responsibility_sub_list')
		{
			return $this->_query_reader->get_list('get_census_responsibility', array('census_id'=>$instructions['census_id']));
		}
		else if(!empty($instructions['action']) && $instructions['action'] == 'verify')
		{
			return $this->_query_reader->get_list('get_census_list', array('search_query'=>$searchString, 'limit_text'=>$start.','.($count+1), 'order_by'=>" C.last_updated ASC, C.date_added ASC "));
		}
		else
		{
			return $this->_query_reader->get_list('get_census_list', array('search_query'=>$searchString." AND C.status='active' ", 'limit_text'=>$start.','.($count+1), 'order_by'=>" C.last_updated ASC, C.date_added ASC "));
		}
	}





	# Approve or reject a census
	function verify($instructions)
	{
		$result = array('boolean'=>false, 'msg'=>'ERROR: The census instructions could not be resolved.');

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

				case 'archive':
					$result['boolean'] = $this->change_status($instructions['id'], 'inactive');
				break;

				case 'restore':
					$result['boolean'] = $this->change_status($instructions['id'], 'pending');
				break;
			}

			if(!empty($result['boolean']))
			{
				$result['msg'] = $result['boolean']? "The census status has been changed": "ERROR: The census status could not be changed.";
			}
		}

		return $result;
	}






	# Change the status of the census
	function change_status($censusId, $newStatus)
	{
		$census = $this->_query_reader->get_row_as_array('get_census_list', array('search_query'=>" C.id='".$censusId."' ", 'limit_text'=>'1', 'order_by'=>' P.last_name '));

		$result1 = !in_array($newStatus, array('pending'))?
			$this->_messenger->send($census['added_by'], array('code'=>'notify_change_of_data_status', 'item'=>'census', 'details'=>"Teacher Name: ".$census['teacher_name']." <br>Period: ".format_date($census['start_date'],'d-M-Y').' to '.format_date($census['end_date'],'d-M-Y')." <br>Average Weekly Work Load: ".$census['weekly_workload_average'], 'status'=>strtoupper($newStatus), 'approver_name'=>($this->native_session->get('__last_name').' '.$this->native_session->get('__first_name')), 'action_date'=>date('d-M-Y h:ia T', strtotime('now')) ))
			: true;

		$result2 = $this->_query_reader->run('update_item_status', array('item_id'=>$censusId, 'table_name'=>'census', 'status'=>$newStatus, 'updated_by'=>$this->native_session->get('__user_id') ));

		return get_decision(array($result1,$result2), FALSE);
	}





	# Reject a census application
	function reject($censusId, $reason)
	{
		$census = $this->_query_reader->get_row_as_array('get_census_list', array('search_query'=>" C.id='".$censusId."' ", 'limit_text'=>'1', 'order_by'=>' P.last_name '));

		$result1 = $this->_messenger->send($census['added_by'], array('code'=>'notify_change_of_data_status', 'item'=>'census', 'details'=>"REASON FOR REJECTION:<br> ".$reason."<br>Please resubmit with reasons fixed.<br><br>Teacher Name: ".$census['teacher_name']." <br>Period: ".format_date($census['start_date'],'d-M-Y').' to '.format_date($census['end_date'],'d-M-Y')." <br>Average Weekly Work Load: ".$census['weekly_workload_average'], 'status'=>'REJECTED', 'approver_name'=>($this->native_session->get('__last_name').' '.$this->native_session->get('__first_name')), 'action_date'=>date('d-M-Y h:ia T', strtotime('now')) ));

		$result2 = $this->_query_reader->run('delete_census_data', array('census_id'=>$censusId));

		return get_decision(array($result1,$result2), FALSE);
	}
}


?>
