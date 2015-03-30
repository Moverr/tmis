<?php echo !empty($msg)?format_notice($this,$msg): ""; ?> 
<table border="0" cellspacing="0" cellpadding="0" class="listtable">
<?php 
if(!empty($list))
{
	$rowsPerPage = !empty($pagecount)? $pagecount: NUM_OF_ROWS_PER_PAGE;
	$listCount = count($list);
	$page = !empty($page)? $page: 1;
	$stop = ($rowsPerPage >= $listCount && !empty($listid))? "<input name='paginationdiv__".$listid."_stop' id='paginationdiv__".$listid."_stop' type='hidden' value='".$page."' />": "";
	
	echo "<tr class='header'><td>&nbsp;</td><td>Teacher</td><td>Period</td><td>Average Workload</td><td>Training</td><td>Responsibilities</td><td>Last Updated</td></tr>";
	
	# Pick the lesser of the two - since if there is a next page, the list count will come with an extra row
	$maxRows = $listCount < $rowsPerPage? $listCount: $rowsPerPage;
	$listType = " data-type='census' ";
	
	for($i=0; $i<$maxRows; $i++)
	{
		$row = $list[$i];
		echo "<tr class='listrow' ".($i%2 == 1? "style='background-color:#F0F0F0;'": "").">
    <td width='1%' style='padding:0px; padding-top:5px; vertical-align:top;'>";
	
	
	if(!empty($action) && $action == 'verify')
	{
		if($row['status'] == 'pending'){
			echo "<div data-val='approve__".$row['id']."' ".$listType." class='approverow confirm' title='Click to approve'></div>
			<div data-val='reject__".$row['id']."' ".$listType." class='rejectrow' title='Click to reject'></div>";
		}
		else if($row['status'] == 'active'){
			echo "<div data-val='archive__".$row['id']."' ".$listType." class='archiverow confirm' title='Click to archive'></div>";
		}
		else if($row['status'] == 'inactive'){
			echo "<div data-val='restore__".$row['id']."' ".$listType." class='restorerow confirm' title='Click to restore'></div>";
		}
	}
	
	echo "</td> 
	<td><a href='".base_url()."teacher/add/id/".$row['teacher_id']."/action/view' class='shadowbox closable'>".$row['teacher_name']."</a></td> 
	<td>".format_date($row['start_date'],'d-M-Y').' to '.format_date($row['end_date'],'d-M-Y')."</td>
	<td>".$row['weekly_workload_average']."</td>
	<td><a href='".base_url()."census/sub_lists/id/".$row['id']."/type/training' class='shadowbox closable'>Training</a></td>
	<td><a href='".base_url()."census/sub_lists/id/".$row['id']."/type/responsibility' class='shadowbox closable'>Responsibilities</a></td>
	<td>".format_date($row['last_updated'],'d-M-Y h:ia T').
	
	((check_access($this, 'submit_teacher_census_data', 'boolean') && !empty($action) && $action == 'verify')? "<div class='rightnote'><a href='".base_url()."census/add/id/".$row['id']."/action/verify' class='shadowbox'>edit</a></div>": "")
	
	."</td></tr>
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