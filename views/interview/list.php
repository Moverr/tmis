<?php echo !empty($msg)?format_notice($this,$msg): ""; ?> 
<table border="0" cellspacing="0" cellpadding="0" class="listtable">
<?php 
if(!empty($list))
{
	$rowsPerPage = !empty($pagecount)? $pagecount: NUM_OF_ROWS_PER_PAGE;
	$listCount = count($list);
	$page = !empty($page)? $page: 1;
	$stop = ($rowsPerPage >= $listCount && !empty($listid))? "<input name='paginationdiv__".$listid."_stop' id='paginationdiv__".$listid."_stop' type='hidden' value='".$page."' />": "";
	
	# Determine which header to use
	if(!empty($action) && $action == 'shortlist')
	{
		echo "<tr class='header'><td>&nbsp;</td><td>Shortlist Name</td><td>Job</td><td>School</td><td>List</td></tr>";
	}
	else if(!empty($action) && in_array($action, array('setdate', 'recommend', 'recommendations')))
	{
		echo "<tr class='header'><td>&nbsp;</td><td>Job</td><td>Institution</td><td>Applicant</td><td>Application Date</td><td>Recommendations</td></tr>";
	}
	else 
	{
		echo "<tr class='header'><td>&nbsp;</td><td>Job</td><td>Applicant</td><td>Interviewer</td><td>Date</td><td>Duration</td><td>Result</td><td>Notes</td></tr>";
	}
	
	# Pick the lesser of the two - since if there is a next page, the list count will come with an extra row
	$maxRows = $listCount < $rowsPerPage? $listCount: $rowsPerPage;
	$listType = " data-type='interview' ";
	
	for($i=0; $i<$maxRows; $i++)
	{
		$row = $list[$i];
		echo "<tr class='listrow' ".($i%2 == 1? "style='background-color:#F0F0F0;'": "").">
    <td width='1%' style='padding:0px; padding-top:5px; vertical-align:top;'>";
	
	
	if(!empty($action) && $action == 'cancel')
	{
		echo "<div data-val='reject__".$row['id']."' ".$listType." class='rejectrow' title='Click to reject'></div>";
	}
	
	if(!empty($action) && $action == 'setdate')
	{
		echo "<input id='interview_".$row['application_id']."' name='selectall[]' type='checkbox' value='".$row['application_id']."' class='bigcheckbox' onChange=\"addListFieldValue('interview_".$row['application_id']."', 'input_multiuserdateset__div','interviewuser','".$row['applicant_id']."|applicationid=".$row['application_id']."','".$row['applicant_name']."')\"><label id='interviewlabel_".$row['application_id']."' for='interview_".$row['application_id']."' class='listcheckbox' style='margin-left:10px;".($this->native_session->get('__select_multi')? "": "display:none;")."'></label>";
	}
	
	
	# User is viewing candidate shortlists
	if(!empty($action) && $action == 'shortlist')
	{
		echo "</td> 
		<td>".$row['shortlist_name']."</td> 
		<td>".$row['job']."</td> 
		<td>".$row['institution_name']."</td>
		<td><a href='".base_url()."interview/shortlist/vacancy/".$row['id']."/name/".encrypt_value($row['shortlist_name'])."' class='shadowbox closable'>Shortlist</a></td>";
	}
	
	
	# User is viewing candidate applications
	else if(!empty($action) && in_array($action, array('setdate', 'recommend', 'recommendations')))
	{
		$cancel = ($action == 'setdate')? " onclick=\"clickIfVisible('interviewlabel_".$row['application_id']."','cancelsetdate')\"": "";
		
		echo "</td> <td>".$row['topic']."<br>(".$row['role_name'].")</td> 
		<td>".$row['institution_name']."</td>
		<td><a href='".base_url()."teacher/add/id/".$row['applicant_id']."/action/view' ".$cancel." class='shadowbox closable'>".$row['applicant_name']."</a></td>
		<td>".date('d-M-Y h:ia T', strtotime($row['submission_date']))."</td>
		<td>".($row['recommendation_count'] > 0? "<a href='".base_url()."interview/recommendations/id/".$row['application_id']."' ".$cancel." class='shadowbox closable'>Recommendations</a>": "No recommendations");
		if($action == 'recommend' && $row['has_recommended'] == 'N') echo "<br><div class='rightnote'><a href='".base_url()."interview/recommend/id/".$row['application_id']."' ".$cancel." class='shadowbox'>recommend</a></div>";
		
		if($action == 'setdate') echo "<br><div class='rightnote'><a href='".base_url()."interview/set_date/id/".$row['application_id']."' ".$cancel." class='shadowbox'>set date</a></div>";
		
		echo "</td>";
	}
	
	# User is viewing the interview list
	else 
	{
		echo "</td> <td>".$row['job']."</td> 
		<td><a href='".base_url()."teacher/add/id/".$row['applicant_id']."/action/view' class='shadowbox closable'>".$row['applicant']."</a></td>
		<td>".$row['interviewer'].(!empty($row['board_id'])? "<br>[<a href='".base_url()."interview/select_board/id/".$row['id']."/view/Y' class='shadowbox closable'>Interview Board</a>]": "")
		
		."</td>
		<td>".date('d-M-Y h:ia T', strtotime($row['interview_date']))."</td>
		<td>".(!empty($row['interview_duration'])? $row['interview_duration']."min": "unspecified")."</td>
		<td>".$row['result']."</td>
		<td>".(!empty($row['note_count'])? "<a href='".base_url()."interview/notes/id/".$row['id']."' class='shadowbox closable'>Notes</a>": "No notes");
		if($action == 'addresult') echo "<br><div class='rightnote'><a href='".base_url()."interview/set_result/id/".$row['id']."' class='shadowbox'>set result</a></div> &nbsp; <div class='rightnote'><a href='".base_url()."interview/add_note/id/".$row['id']."' class='shadowbox'>add note</a></div> &nbsp; <div class='rightnote'><a href='".base_url()."interview/select_board/id/".$row['id']."' class='shadowbox'>select board</a></div>";
		echo "</td>";
	
	}
	
	echo "</tr>
	
	<tr><td style='padding:0px;'></td><td colspan='6' style='padding:0px;'><div id='action__".$row['id']."' class='actionrowdiv' style='display:none;'></div>".$stop."</td></tr>";
	
	}  
}
else
{
	$stop = "<input name='paginationdiv__".$listid."_stop' id='paginationdiv__".$listid."_stop' type='hidden' value='1' />";
	echo "<tr><td>".format_notice($this,'WARNING: There are no items in this list.').$stop."</td></tr>";
}
?>

  
</table>