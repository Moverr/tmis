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
	if(!empty($action) && $action == 'post')
	{
		echo "<tr class='header'><td>&nbsp;</td><td>Name</td><td>Job Position</td><td>School</td><td>Date Awarded</td></tr>";
		$colSpan = 4;
	}
	else 
	{
		echo "<tr class='header'><td>&nbsp;</td><td>Name</td><td>Job Position</td><td>School</td><td>Start Date</td><td>Status</td></tr>";
		$colSpan = 5;
	}
	
	# Pick the lesser of the two - since if there is a next page, the list count will come with an extra row
	$maxRows = $listCount < $rowsPerPage? $listCount: $rowsPerPage;
	$listType = " data-type='confirmation' ";
	
	for($i=0; $i<$maxRows; $i++)
	{
		$row = $list[$i];
		echo "<tr class='listrow' ".($i%2 == 1? "style='background-color:#F0F0F0;'": "").">
    <td width='1%' style='padding:0px; padding-top:5px; vertical-align:top;'>";
	
	
	if(!empty($action) && $action == 'approve' && !$this->native_session->get('__nosignature'))
	{
		echo "<div data-val='approve_toapprove__".$row['id']."' ".$listType." class='approverow' title='Click to approve'></div>
			<div data-val='reject_fromapprove__".$row['id']."' ".$listType." class='rejectrow' title='Click to reject'></div>";
	}
	else if(!empty($action) && $action == 'verify')
	{
		if($row['confirmation_status'] == 'confirmed') 
		{
			echo "<div data-val='approve_toverify__".$row['id']."' ".$listType." class='approverow confirm' title='Click to approve'></div>
			<div data-val='reject_fromverify__".$row['id']."' ".$listType." class='rejectrow' title='Click to reject'></div>";
		}
		#else echo "<div data-val='archive__".$row['id']."' ".$listType." class='archiverow confirm' title='Click to archive'></div>";
	}
	else if(!empty($action) && $action == 'post')
	{
		echo "<div data-val='approve__".$row['id']."' ".$listType." class='approverow confirm' title='Click to approve'></div>
			<div data-val='reject__".$row['id']."' ".$listType." class='rejectrow' title='Click to reject'></div>";
	}
	
	
	# User is viewing candidate shortlists
	if(!empty($action) && $action == 'post')
	{
		echo "</td> 
		<td><a href='".base_url()."teacher/add/id/".$row['postee_id']."/action/view' class='shadowbox closable'>".$row['teacher_name']."</a></td> 
		<td>".$row['job']."</td> 
		<td>".$row['institution_name']."</td>
		<td>".date('d-M-Y h:ia T', strtotime($row['award_date']))."</td>";
	}
	
	
	# User is viewing confirmation list
	else 
	{
		echo "</td> 
		<td><a href='".base_url()."teacher/add/id/".$row['postee_id']."/action/view' class='shadowbox closable'>".$row['teacher_name']."</a></td> 
		<td>".$row['job']."</td> 
		<td>".$row['institution_name']."</td>
		<td>".date('d-M-Y h:ia T', strtotime($row['start_date']))."</td>
		<td>".strtoupper($row['confirmation_status'])."</td>";
	}
	
	echo "</tr>
	
	<tr><td style='padding:0px;'></td><td colspan='".$colSpan."' style='padding:0px;'><div id='action__".$row['id']."' class='actionrowdiv' style='display:none;'></div>".$stop."</td></tr>";
	
	}  
}
else
{
	$stop = "<input name='paginationdiv__".$listid."_stop' id='paginationdiv__".$listid."_stop' type='hidden' value='1' />";
	echo "<tr><td>".format_notice($this,'WARNING: There are no items in this list.').$stop."</td></tr>";
}
?>

  
</table>