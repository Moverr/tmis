<?php echo !empty($msg)?format_notice($this,$msg): ""; ?> 
<table border="0" cellspacing="0" cellpadding="0" class="listtable">
<?php 
if(!empty($list))
{
	$rowsPerPage = !empty($pagecount)? $pagecount: NUM_OF_ROWS_PER_PAGE;
	$listCount = count($list);
	$page = !empty($page)? $page: 1;
	$stop = ($rowsPerPage >= $listCount && !empty($listid))? "<input name='paginationdiv__".$listid."_stop' id='paginationdiv__".$listid."_stop' type='hidden' value='".$page."' />": "";
	
	if($action == 'payrollreport')
	{
		echo "<tr class='header'><td>&nbsp;</td><td>Registration Number</td><td>Name</td><td>Title</td><td>Salary Scale</td><td>Birth Date</td><td>Status</td><td>Proposed Retirement</td></tr>";
	}
	else if($action == 'cimreport')
	{
		echo "<tr class='header'><td>&nbsp;</td><td>Registration Number</td><td>Name</td><td>Title</td><td>Responsibility</td><td>Birth Date</td><td>Location</td><td>School</td><td>School Type</td></tr>";
	}
	else
	{
		echo "<tr class='header'><td>&nbsp;</td><td>Name</td><td>Age</td><td>School</td><td>Location</td><td>Telephone</td><td>Email</td><td>Teacher Status</td><td>Last Updated</td></tr>";
	}
	
	# Pick the lesser of the two - since if there is a next page, the list count will come with an extra row
	$maxRows = $listCount < $rowsPerPage? $listCount: $rowsPerPage;
	$listType = " data-type='teacher' ";
	
	for($i=0; $i<$maxRows; $i++)
	{
		$row = $list[$i];
		echo "<tr class='listrow' ".($i%2 == 1? "style='background-color:#F0F0F0;'": "").">
    <td width='1%' style='padding:0px; padding-top:5px; vertical-align:top;'>";
	
	
	if(!empty($action) && $action == 'verify')
	{
		if($row['teacher_status'] == 'completed'){
			echo "<div data-val='approve_toapproved__".$row['id']."' ".$listType." class='approverow' title='Click to approve'></div>
			<div data-val='reject_fromcompleted__".$row['id']."' ".$listType." class='rejectrow' title='Click to reject'></div>";
		}
		else if($row['teacher_status'] == 'active')
		{
			echo "<div data-val='archive__".$row['id']."' ".$listType." class='archiverow confirm' title='Click to archive'></div>";
		}
		else if($row['teacher_status'] == 'archived')
		{
			echo "<div data-val='restore__".$row['id']."' ".$listType." class='restorerow confirm' title='Click to restore'></div>";
		}
	}
	else  if(!empty($action) && $action == 'approve')
	{
		if($row['teacher_status'] == 'approved' && !$this->native_session->get('__nosignature')){
			echo "<div data-val='approve_toactive__".$row['id']."' ".$listType." class='approverow' title='Click to approve'></div>
			<div data-val='reject_fromapproved__".$row['id']."' ".$listType." class='rejectrow' title='Click to reject'></div>";
		}
	}
	
	echo "</td>";
	
	
	if($action == 'payrollreport')
	{
	  echo "<td>".$row['file_number']."</td>
			<td>".$row['name']."</td>
			<td>".$row['title']."</td>
			<td>".$row['salary_scale']."</td>
			<td>".format_date($row['date_of_birth'],'d-M-Y')."</td>
			<td>".$row['teacher_status']."</td>
			<td>".format_date($row['proposed_retirement'],'d-M-Y').$stop."</td>
		   </tr>";
	}
	else if($action == 'cimreport')
	{
		echo "<td>".$row['file_number']."</td>
			<td>".$row['name']."</td>
			<td>".$row['title']."</td>
			<td>".$row['responsibility']."</td>
			<td>".format_date($row['date_of_birth'],'d-M-Y')."</td>
			<td>".$row['location']."</td>
			<td>".$row['school']."</td>
			<td>".$row['school_type'].$stop."</td>
			</tr>";
	}
	else
	{
		echo "<td>".$row['name']."</td> 
			<td style='".format_age($row['age'])."'>".$row['age']." yrs".format_age($row['age'],'timeleft')."</td>
			<td>".(!empty($row['school'])? $row['school']: '&nbsp;')."</td>
			<td>".(!empty($row['school_address'])? $row['school_address']: '&nbsp;')."</td>
			<td>".$row['telephone']."</td>
			<td>".$row['email_address']."</td>
			<td>".strtoupper($row['teacher_status'])."</td>
			<td>".format_date($row['last_updated'],'d-M-Y h:ia T').
	
			"<br><div class='rightnote'><a href='".base_url()."teacher/add/id/".$row['id']."/action/view' class='shadowbox closable'>details</a></div>".
	
			((check_access($this, 'add_new_teacher', 'boolean') && !empty($action) && $action == 'view')? "<div class='rightnote'><a href='".base_url()."teacher/add/id/".$row['id']."/action/verify' class='shadowbox'>edit</a></div>": "")
	
			."</td></tr>
	<tr><td style='padding:0px;'></td><td colspan='7' style='padding:0px;'><div id='action__".$row['id']."' class='actionrowdiv' style='display:none;'></div>".$stop."</td></tr>";
		}
	}  
}
else
{
	$stop = "<input name='paginationdiv__".$listid."_stop' id='paginationdiv__".$listid."_stop' type='hidden' value='1' />";
	echo "<tr><td>".format_notice($this,'WARNING: There are no items in this list.').$stop."</td></tr>";
}
?>

  
</table>