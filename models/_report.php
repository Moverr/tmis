<?php
/**
 * Handles and generates reports in the system.
 *
 * @author Al Zziwa <azziwa@gmail.com>
 * @version 1.1.0
 * @copyright TMIS
 * @created 01/08/2015
 */
class _report extends CI_Model
{
	
	# Get list of reports
	function get_list($instructions=array())
	{
		$searchString = " 1=1 ";
		if(!empty($instructions['action']) && $instructions['action']== 'user')
		{
			$searchString = " L.log_code LIKE 'user_%' ";
		}
		else if(!empty($instructions['action']) && $instructions['action']== 'system')
		{
			$searchString = " L.log_code LIKE 'system_%' ";
		}
		
		
		# If a search phrase is sent in the instructions
		if(!empty($instructions['searchstring']))
		{
			$searchString .= " AND ".$instructions['searchstring'];
		}
		
		# Instructions
		$count = !empty($instructions['pagecount'])? $instructions['pagecount']: NUM_OF_ROWS_PER_PAGE;
		$start = !empty($instructions['page'])? ($instructions['page']-1)*$count: 0;
		
		return $this->_query_reader->get_list('get_log_list_data',array('search_query'=>$searchString, 'limit_text'=>$start.','.($count+1), 'order_by'=>" L.date_added DESC "));
	}
	



}


?>